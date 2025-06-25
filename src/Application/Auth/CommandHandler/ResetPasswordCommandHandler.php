<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\CommandHandler;

use CompanyOS\Domain\Auth\Application\Command\ResetPasswordCommand;
use CompanyOS\Domain\Auth\Application\Event\PasswordResetCompletedEvent;
use CompanyOS\Domain\Auth\Domain\Service\AuthenticationService;
use CompanyOS\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class ResetPasswordCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly AuthenticationService $authService,
        private readonly MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(ResetPasswordCommand $command): void
    {
        $resetResult = $this->authService->resetPassword($command->token, $command->newPassword);
        
        // Application Event auslösen
        $this->eventBus->dispatch(new PasswordResetCompletedEvent(
            userId: $resetResult['userId'],
            email: $resetResult['email'],
            resetToken: $command->token,
            occurredAt: new \DateTimeImmutable()
        ));
    }
} 