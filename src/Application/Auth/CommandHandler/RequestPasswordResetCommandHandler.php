<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Auth\Command\RequestPasswordResetCommand;
use CompanyOS\Bundle\CoreBundle\Application\Auth\Event\PasswordResetRequestedEvent;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Service\AuthenticationService;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class RequestPasswordResetCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly AuthenticationService $authService,
        private readonly MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(RequestPasswordResetCommand $command): void
    {
        $resetResult = $this->authService->requestPasswordReset($command->emailOrUsername);
        
        // Application Event auslösen
        $this->eventBus->dispatch(new PasswordResetRequestedEvent(
            email: $command->emailOrUsername,
            resetToken: $resetResult['token'],
            expiresAt: $resetResult['expiresAt'],
            occurredAt: new \DateTimeImmutable()
        ));
    }
} 