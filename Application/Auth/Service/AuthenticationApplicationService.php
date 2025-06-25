<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\Service;

use CompanyOS\Domain\Auth\Application\Command\ChangePasswordCommand;
use CompanyOS\Domain\Auth\Application\Command\LoginUserCommand;
use CompanyOS\Domain\Auth\Application\Command\LogoutUserCommand;
use CompanyOS\Domain\Auth\Application\Command\RefreshTokenCommand;
use CompanyOS\Domain\Auth\Application\Command\RequestPasswordResetCommand;
use CompanyOS\Domain\Auth\Application\Command\ResetPasswordCommand;
use CompanyOS\Domain\Auth\Application\DTO\ChangePasswordRequest;
use CompanyOS\Domain\Auth\Application\DTO\LoginRequest;
use CompanyOS\Domain\Auth\Application\DTO\RefreshTokenRequest;
use CompanyOS\Domain\Auth\Application\DTO\RequestPasswordResetRequest;
use CompanyOS\Domain\Auth\Application\DTO\ResetPasswordRequest;
use CompanyOS\Domain\Auth\Application\Query\GetActiveSessionsQuery;
use CompanyOS\Domain\Auth\Application\Query\GetOAuthClientsQuery;
use CompanyOS\Domain\Auth\Application\Query\GetUserProfileQuery;
use CompanyOS\Domain\Auth\Application\Query\ValidateTokenQuery;
use CompanyOS\Application\Command\CommandBusInterface;
use CompanyOS\Application\Query\QueryBusInterface;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

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