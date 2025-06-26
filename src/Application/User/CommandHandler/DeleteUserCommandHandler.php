<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\User\Command\DeleteUserCommand;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
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