<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Middleware;

use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OAuth2Middleware implements MiddlewareInterface
{
    public function __construct(
        private ResourceServer $resourceServer
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            // Token validieren
            $request = $this->resourceServer->validateAuthenticatedRequest($request);
            
            // Request an Handler weiterleiten
            return $handler->handle($request);
            
        } catch (OAuthServerException $exception) {
            return new \Nyholm\Psr7\Response(
                $exception->getHttpStatusCode(),
                ['Content-Type' => 'application/json'],
                json_encode([
                    'error' => $exception->getErrorType(),
                    'message' => $exception->getMessage(),
                    'hint' => $exception->getHint()
                ])
            );
        }
    }
} 