<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Controller;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'OAuth2', description: 'OAuth2 authentication endpoints')]
class OAuthController extends AbstractController
{
    public function __construct(
        private AuthorizationServer $authorizationServer,
        private \CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence\LeagueAccessTokenRepository $accessTokenRepository,
        private \CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence\LeagueRefreshTokenRepository $refreshTokenRepository
    ) {
    }

    #[Route('/token', methods: ['POST'])]
    #[OA\Post(
        path: '/token',
        summary: 'OAuth2 Token holen',
        description: 'Erhalte ein Access Token via password, client_credentials oder refresh_token Grant. Content-Type: application/x-www-form-urlencoded.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['grant_type', 'client_id'],
                    properties: [
                        new OA\Property(property: 'grant_type', type: 'string', example: 'password'),
                        new OA\Property(property: 'client_id', type: 'string', example: 'frontend-client'),
                        new OA\Property(property: 'client_secret', type: 'string', example: 'secret', nullable: true),
                        new OA\Property(property: 'username', type: 'string', example: 'user@example.com', nullable: true),
                        new OA\Property(property: 'password', type: 'string', example: 'password', nullable: true),
                        new OA\Property(property: 'refresh_token', type: 'string', example: '...', nullable: true),
                        new OA\Property(property: 'scope', type: 'string', example: 'basic user.read', nullable: true)
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token response',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'access_token', type: 'string'),
                            new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                            new OA\Property(property: 'expires_in', type: 'integer'),
                            new OA\Property(property: 'refresh_token', type: 'string'),
                            new OA\Property(property: 'scope', type: 'string')
                        ]
                    )
                )
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public function token(Request $request): Response
    {
        // Debug-Logging für eingehende Requests
        error_log('[OAuth2] Token-Request eingegangen: ' . json_encode($request->request->all()));
        error_log('[OAuth2] Request Headers: ' . json_encode($request->headers->all()));
        
        try {
            // Request in PSR-7 Format konvertieren
            $psrRequest = $this->createPsrRequest($request);
            
            // Token generieren
            $response = $this->authorizationServer->respondToAccessTokenRequest($psrRequest, new \Nyholm\Psr7\Response());
            
            // Response in Symfony Format konvertieren
            return $this->createSymfonyResponse($response);
            
        } catch (OAuthServerException $exception) {
            error_log('[OAuth2] OAuthServerException: ' . $exception->getErrorType() . ' - ' . $exception->getMessage());
            error_log('[OAuth2] OAuthServerException Hint: ' . $exception->getHint());
            error_log('[OAuth2] OAuthServerException Stack: ' . $exception->getTraceAsString());
            
            return $this->json([
                'error' => $exception->getErrorType(),
                'message' => $exception->getMessage(),
                'hint' => $exception->getHint()
            ], $exception->getHttpStatusCode());
            
        } catch (\Exception $exception) {
            // Log the actual exception for debugging
            error_log('[OAuth2] General Exception: ' . $exception->getMessage());
            error_log('[OAuth2] General Exception Stack: ' . $exception->getTraceAsString());
            
            return $this->json([
                'error' => 'server_error',
                'message' => 'Internal server error',
                'debug' => $exception->getMessage() // Temporär für Debugging
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/revoke', methods: ['POST'])]
    #[OA\Post(
        path: '/revoke',
        summary: 'OAuth2 Token widerrufen (Logout)',
        description: 'Revokiert ein Access- oder Refresh-Token. Content-Type: application/x-www-form-urlencoded.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['token'],
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: 'access_token_or_refresh_token'),
                        new OA\Property(property: 'token_type_hint', type: 'string', example: 'access_token', nullable: true)
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Token erfolgreich widerrufen'),
            new OA\Response(response: 400, description: 'Bad request')
        ]
    )]
    public function revoke(Request $request): Response
    {
        $token = $request->request->get('token');
        $tokenTypeHint = $request->request->get('token_type_hint', 'access_token');

        if (!$token) {
            return $this->json(['error' => 'invalid_request', 'message' => 'Token is required'], 400);
        }

        if ($tokenTypeHint === 'access_token') {
            $this->accessTokenRepository->revokeAccessToken($token);
        } elseif ($tokenTypeHint === 'refresh_token') {
            $this->refreshTokenRepository->revokeRefreshToken($token);
        } else {
            return $this->json(['error' => 'invalid_request', 'message' => 'Unknown token_type_hint'], 400);
        }

        return $this->json(['message' => 'Token revoked successfully']);
    }

    private function createPsrRequest(Request $request): ServerRequestInterface
    {
        $psrRequest = new \Nyholm\Psr7\ServerRequest(
            $request->getMethod(),
            $request->getUri(),
            $request->headers->all(),
            $request->getContent(),
            $request->getProtocolVersion(),
            $request->server->all()
        );

        // POST-Parameter hinzufügen
        $psrRequest = $psrRequest->withParsedBody($request->request->all());

        return $psrRequest;
    }

    private function createSymfonyResponse(ResponseInterface $psrResponse): Response
    {
        $content = $psrResponse->getBody()->getContents();
        $headers = $psrResponse->getHeaders();
        
        $response = new Response($content, $psrResponse->getStatusCode());
        
        foreach ($headers as $name => $values) {
            $response->headers->set($name, $values);
        }
        
        return $response;
    }
} 