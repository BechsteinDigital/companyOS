<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Auth\Command\LoginUserCommand;
use CompanyOS\Bundle\CoreBundle\Application\Auth\DTO\LoginResponse;
use CompanyOS\Bundle\CoreBundle\Application\Auth\Event\LoginFailedEvent;
use CompanyOS\Bundle\CoreBundle\Application\Auth\Event\UserLoggedInEvent;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Service\AuthenticationService;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class LoginUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly AuthenticationService $authService,
        private readonly UserRepositoryInterface $userRepository,
        private readonly MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(LoginUserCommand $command): LoginResponse
    {
        try {
            $user = $this->authService->authenticateUser($command->email, $command->password);
            
            if (!$user) {
                $this->eventBus->dispatch(new LoginFailedEvent(
                    email: $command->email,
                    clientId: $command->clientId ?? 'unknown',
                    reason: 'Invalid credentials',
                    ipAddress: $command->ipAddress ?? 'unknown',
                    occurredAt: new \DateTimeImmutable()
                ));
                
                throw new \InvalidArgumentException('Login fehlgeschlagen: E-Mail oder Passwort falsch.');
            }

            // Token-Generierung (Access/Refresh) und ggf. weitere Logik
            $tokens = $this->authService->generateTokensForUser($user, $command->clientId, $command->scopes);
            
            // Application Event auslösen
            $this->eventBus->dispatch(new UserLoggedInEvent(
                userId: $user->getId(),
                email: $user->getEmail()->value(),
                clientId: $command->clientId ?? 'default',
                scopes: $command->scopes,
                accessToken: $tokens['accessToken'],
                refreshToken: $tokens['refreshToken'],
                expiresAt: $tokens['expiresAt'],
                occurredAt: new \DateTimeImmutable()
            ));

            return new LoginResponse($user, $tokens['accessToken'], $tokens['refreshToken']);
            
        } catch (\Exception $e) {
            $this->eventBus->dispatch(new LoginFailedEvent(
                email: $command->email,
                clientId: $command->clientId ?? 'unknown',
                reason: $e->getMessage(),
                ipAddress: $command->ipAddress ?? 'unknown',
                occurredAt: new \DateTimeImmutable()
            ));
            
            throw $e;
        }
    }
} 