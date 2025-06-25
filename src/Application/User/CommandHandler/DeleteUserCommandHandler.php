<?php

namespace CompanyOS\Domain\User\Application\CommandHandler;

use CompanyOS\Domain\User\Application\Command\DeleteUserCommand;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Domain\ValueObject\Uuid;
use CompanyOS\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(DeleteUserCommand $command): void
    {
        $userId = new Uuid($command->id);
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        // Soft delete - mark as deleted but keep in database
        $user->delete();
        $this->userRepository->save($user);
    }
} 