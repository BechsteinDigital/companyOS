<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Auth\Command\ChangePasswordCommand;
use CompanyOS\Bundle\CoreBundle\Application\Auth\Event\PasswordChangedEvent;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Service\AuthenticationService;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class ChangePasswordCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly AuthenticationService $authService,
        private readonly UserRepositoryInterface $userRepository,
        private readonly MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(ChangePasswordCommand $command): void
    {
        $this->authService->changePassword($command->userId, $command->oldPassword, $command->newPassword);
        
        // User für Event abrufen
        $user = $this->userRepository->findById($command->userId);
        
        // Application Event auslösen
        $this->eventBus->dispatch(new PasswordChangedEvent(
            userId: $command->userId,
            email: $user->getEmail()->value(),
            occurredAt: new \DateTimeImmutable()
        ));
    }
} 