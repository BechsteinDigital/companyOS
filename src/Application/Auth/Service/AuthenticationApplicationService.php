<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\Service;

use CompanyOS\Application\Auth\Command\ChangePasswordCommand;
use CompanyOS\Application\Auth\Command\LoginUserCommand;
use CompanyOS\Application\Auth\Command\LogoutUserCommand;
use CompanyOS\Application\Auth\Command\RefreshTokenCommand;
use CompanyOS\Application\Auth\Command\RequestPasswordResetCommand;
use CompanyOS\Application\Auth\Command\ResetPasswordCommand;
use CompanyOS\Application\Auth\DTO\ChangePasswordRequest;
use CompanyOS\Application\Auth\DTO\LoginRequest;
use CompanyOS\Application\Auth\DTO\RefreshTokenRequest;
use CompanyOS\Application\Auth\DTO\RequestPasswordResetRequest;
use CompanyOS\Application\Auth\DTO\ResetPasswordRequest;
use CompanyOS\Application\Auth\Query\GetActiveSessionsQuery;
use CompanyOS\Application\Auth\Query\GetOAuthClientsQuery;
use CompanyOS\Application\Auth\Query\GetUserProfileQuery;
use CompanyOS\Application\Auth\Query\ValidateTokenQuery;
use CompanyOS\Application\Command\CommandBusInterface;
use CompanyOS\Application\Query\QueryBusInterface;
use CompanyOS\Domain\ValueObject\Uuid;

final class AuthenticationApplicationService
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus
    ) {
    }

    public function login(LoginRequest $request): void
    {
        $command = new LoginUserCommand(
            email: $request->email,
            password: $request->password,
            clientId: $request->clientId,
            scopes: $request->scopes
        );

        $this->commandBus->dispatch($command);
    }

    public function logout(string $accessToken): void
    {
        $command = new LogoutUserCommand($accessToken);
        $this->commandBus->dispatch($command);
    }

    public function refreshToken(RefreshTokenRequest $request): void
    {
        $command = new RefreshTokenCommand(
            refreshToken: $request->refreshToken,
            clientId: $request->clientId
        );

        $this->commandBus->dispatch($command);
    }

    public function changePassword(ChangePasswordRequest $request): void
    {
        $command = new ChangePasswordCommand(
            userId: Uuid::fromString($request->userId),
            currentPassword: $request->currentPassword,
            newPassword: $request->newPassword,
            confirmPassword: $request->confirmPassword
        );

        $this->commandBus->dispatch($command);
    }

    public function requestPasswordReset(RequestPasswordResetRequest $request): void
    {
        $command = new RequestPasswordResetCommand($request->email);
        $this->commandBus->dispatch($command);
    }

    public function resetPassword(ResetPasswordRequest $request): void
    {
        $command = new ResetPasswordCommand(
            token: $request->token,
            newPassword: $request->newPassword,
            confirmPassword: $request->confirmPassword
        );

        $this->commandBus->dispatch($command);
    }

    public function getUserProfile(string $userId): mixed
    {
        $query = new GetUserProfileQuery(Uuid::fromString($userId));
        return $this->queryBus->ask($query);
    }

    public function getActiveSessions(string $userId): array
    {
        $query = new GetActiveSessionsQuery(Uuid::fromString($userId));
        return $this->queryBus->ask($query);
    }

    public function validateToken(string $accessToken): mixed
    {
        $query = new ValidateTokenQuery($accessToken);
        return $this->queryBus->ask($query);
    }

    public function getOAuthClients(?string $clientId = null, ?string $clientName = null): array
    {
        $query = new GetOAuthClientsQuery($clientId, $clientName);
        return $this->queryBus->ask($query);
    }
} 