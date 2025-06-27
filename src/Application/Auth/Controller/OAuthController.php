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
use Psr\Log\LoggerInterface;

#[OA\Tag(name: 'OAuth2', description: 'OAuth2 authentication endpoints')]
class OAuthController extends AbstractController
{
    public function __construct(
        private AuthorizationServer $authorizationServer,
        private LoggerInterface $logger
    ) {
    }

    #[Route('/api/oauth2/token', name: 'companyos_core_application_auth_oauth_token', methods: ['POST'])]
    #[OA\Post(
        summary: 'Get OAuth2 access token',
        description: 'Authenticate user and get access token using password grant',
        requestBody: new OA\RequestBody(
            description: 'OAuth2 token request parameters',
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['grant_type', 'client_id', 'username', 'password'],
                    properties: [
                        new OA\Property(property: 'grant_type', type: 'string', example: 'password'),
                        new OA\Property(property: 'client_id', type: 'string', example: 'backend'),
                        new OA\Property(property: 'username', type: 'string', example: 'user@example.com'),
                        new OA\Property(property: 'password', type: 'string', example: 'password123'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Access token granted',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                            new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                            new OA\Property(property: 'access_token', type: 'string'),
                            new OA\Property(property: 'refresh_token', type: 'string'),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid request or authentication failed',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'error', type: 'string'),
                            new OA\Property(property: 'message', type: 'string'),
                        ]
                    )
                )
            ),
        ]
    )]
    public function token(Request $request): Response
    {
        // Debug-Logging
        $this->logger->info('[OAuth2] Token-Request eingegangen', [
            'requestData' => $request->request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            // Konvertiere Symfony Request zu PSR-7 Request
            $psrRequest = $this->createPsrRequest($request);
            $psrResponse = $this->createPsrResponse();

            // Verwende Standard OAuth2-Server-Bibliothek
            $response = $this->authorizationServer->respondToAccessTokenRequest($psrRequest, $psrResponse);

            // Debug-Logging für erfolgreiche Authentifizierung
            $this->logger->info('[OAuth2] Token erfolgreich erstellt');

            return $this->createSymfonyResponse($response);

        } catch (OAuthServerException $exception) {
            // Debug-Logging für OAuth2-Fehler
            $this->logger->error('[OAuth2] OAuthServerException', [
                'errorType' => $exception->getErrorType(),
                'message' => $exception->getMessage(),
                'hint' => $exception->getHint(),
                'stack' => $exception->getTraceAsString()
            ]);

            return $this->createErrorResponse($exception);
        } catch (\Exception $exception) {
            // Debug-Logging für allgemeine Fehler
            $this->logger->error('[OAuth2] Allgemeiner Fehler', [
                'message' => $exception->getMessage(),
                'stack' => $exception->getTraceAsString()
            ]);

            return new Response(
                json_encode([
                    'error' => 'server_error',
                    'message' => 'Internal server error'
                ]),
                500,
                ['Content-Type' => 'application/json']
            );
        }
    }

    private function createPsrRequest(Request $request): ServerRequestInterface
    {
        // Einfache PSR-7 Request-Erstellung
        $psrRequest = new \Nyholm\Psr7\ServerRequest(
            $request->getMethod(),
            $request->getUri(),
            $request->headers->all(),
            $request->getContent(),
            '1.1',
            $request->server->all()
        );

        // POST-Parameter hinzufügen
        return $psrRequest->withParsedBody($request->request->all());
    }

    private function createPsrResponse(): ResponseInterface
    {
        return new \Nyholm\Psr7\Response();
    }

    private function createSymfonyResponse(ResponseInterface $psrResponse): Response
    {
        $content = $psrResponse->getBody()->getContents();
        $headers = $psrResponse->getHeaders();

        return new Response(
            $content,
            $psrResponse->getStatusCode(),
            $headers
        );
    }

    private function createErrorResponse(OAuthServerException $exception): Response
    {
        return new Response(
            json_encode([
                'error' => $exception->getErrorType(),
                'message' => $exception->getMessage(),
                'hint' => $exception->getHint()
            ]),
            $exception->getHttpStatusCode(),
            ['Content-Type' => 'application/json']
        );
    }
} 