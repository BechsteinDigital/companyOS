<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\User\Command\BulkDeleteUsersCommand;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class BulkDeleteUsersCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(BulkDeleteUsersCommand $command): void
    {
        $deletedCount = 0;
        $errors = [];

        foreach ($command->userIds as $userId) {
            try {
                $user = $this->userRepository->findById(Uuid::fromString($userId));
                
                if (!$user) {
                    $errors[] = "User with ID {$userId} not found";
                    continue;
                }

                if ($command->force) {
                    // Hard delete
                    $this->userRepository->remove($user);
                } else {
                    // Soft delete
                    $user->delete();
                    $this->userRepository->save($user);
                }
                
                $deletedCount++;
                
            } catch (\Exception $e) {
                $errors[] = "Failed to delete user {$userId}: " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            throw new \RuntimeException(
                "Bulk delete completed with errors. Deleted: {$deletedCount}, Errors: " . implode(', ', $errors)
            );
        }
    }
} 