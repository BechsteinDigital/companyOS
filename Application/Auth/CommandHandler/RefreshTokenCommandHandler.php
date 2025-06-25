<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\CommandHandler;

use CompanyOS\Domain\Auth\Application\Command\RefreshTokenCommand;
use CompanyOS\Domain\Auth\Application\DTO\LoginResponse;
use CompanyOS\Domain\Auth\Application\Event\TokenRefreshedEvent;
use CompanyOS\Domain\Auth\Domain\Service\AuthenticationService;
use CompanyOS\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class RefreshTokenCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly AuthenticationService $authService,
        private readonly MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(RefreshTokenCommand $command): LoginResponse
    {
        $tokens = $this->authService->refreshToken($command->refreshToken, $command->clientId);
        
        // Application Event auslösen
        $this->eventBus->dispatch(new TokenRefreshedEvent(
            userId: $tokens['user']->getId(),
            oldAccessToken: $tokens['oldAccessToken'],
            newAccessToken: $tokens['accessToken'],
            refreshToken: $command->refreshToken,
            clientId: $command->clientId ?? 'default',
            scopes: $tokens['scopes'],
            expiresAt: $tokens['expiresAt'],
            occurredAt: new \DateTimeImmutable()
        ));
        
        return new LoginResponse($tokens['user'], $tokens['accessToken'], $tokens['refreshToken']);
    }
} 