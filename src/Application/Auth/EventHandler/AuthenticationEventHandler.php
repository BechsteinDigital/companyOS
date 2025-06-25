<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\EventHandler;

use CompanyOS\Application\Auth\Event\LoginFailedEvent;
use CompanyOS\Application\Auth\Event\PasswordChangedEvent;
use CompanyOS\Application\Auth\Event\PasswordResetCompletedEvent;
use CompanyOS\Application\Auth\Event\PasswordResetRequestedEvent;
use CompanyOS\Application\Auth\Event\TokenRefreshedEvent;
use CompanyOS\Application\Auth\Event\TokenRevokedEvent;
use CompanyOS\Application\Auth\Event\UserLoggedInEvent;
use CompanyOS\Application\Auth\Event\UserLoggedOutEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AuthenticationEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(UserLoggedInEvent $event): void
    {
        $this->logger->info('User logged in successfully', [
            'userId' => $event->userId->value(),
            'email' => $event->email,
            'clientId' => $event->clientId,
            'scopes' => $event->scopes,
            'expiresAt' => $event->expiresAt->format('Y-m-d H:i:s')
        ]);
    }

    public function handleUserLoggedOut(UserLoggedOutEvent $event): void
    {
        $this->logger->info('User logged out', [
            'userId' => $event->userId->value(),
            'clientId' => $event->clientId,
            'accessToken' => substr($event->accessToken, 0, 10) . '...'
        ]);
    }

    public function handleTokenRefreshed(TokenRefreshedEvent $event): void
    {
        $this->logger->info('Token refreshed', [
            'userId' => $event->userId->value(),
            'clientId' => $event->clientId,
            'expiresAt' => $event->expiresAt->format('Y-m-d H:i:s')
        ]);
    }

    public function handlePasswordChanged(PasswordChangedEvent $event): void
    {
        $this->logger->info('Password changed', [
            'userId' => $event->userId->value(),
            'email' => $event->email
        ]);
    }

    public function handlePasswordResetRequested(PasswordResetRequestedEvent $event): void
    {
        $this->logger->info('Password reset requested', [
            'email' => $event->email,
            'expiresAt' => $event->expiresAt->format('Y-m-d H:i:s')
        ]);
    }

    public function handlePasswordResetCompleted(PasswordResetCompletedEvent $event): void
    {
        $this->logger->info('Password reset completed', [
            'userId' => $event->userId->value(),
            'email' => $event->email
        ]);
    }

    public function handleLoginFailed(LoginFailedEvent $event): void
    {
        $this->logger->warning('Login failed', [
            'email' => $event->email,
            'clientId' => $event->clientId,
            'reason' => $event->reason,
            'ipAddress' => $event->ipAddress
        ]);
    }

    public function handleTokenRevoked(TokenRevokedEvent $event): void
    {
        $this->logger->info('Token revoked', [
            'userId' => $event->userId->value(),
            'clientId' => $event->clientId,
            'reason' => $event->reason
        ]);
    }
} 