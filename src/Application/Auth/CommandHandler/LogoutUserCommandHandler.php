<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Auth\Command\LogoutUserCommand;
use CompanyOS\Bundle\CoreBundle\Application\Auth\Event\UserLoggedOutEvent;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Service\AuthenticationService;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class LogoutUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly AuthenticationService $authService,
        private readonly MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(LogoutUserCommand $command): void
    {
        $logoutResult = $this->authService->logoutUser($command->accessToken);
        
        // Application Event auslösen
        $this->eventBus->dispatch(new UserLoggedOutEvent(
            userId: $logoutResult['userId'],
            accessToken: $command->accessToken,
            clientId: $logoutResult['clientId'],
            occurredAt: new \DateTimeImmutable()
        ));
    }
} 