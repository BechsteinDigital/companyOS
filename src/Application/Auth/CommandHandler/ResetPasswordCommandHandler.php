<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Auth\Command\ResetPasswordCommand;
use CompanyOS\Bundle\CoreBundle\Application\Auth\Event\PasswordResetCompletedEvent;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Service\AuthenticationService;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
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