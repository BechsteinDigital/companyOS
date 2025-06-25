<?php

namespace CompanyOS\Domain\User\Application\CommandHandler;

use CompanyOS\Domain\User\Application\Command\BulkDeleteUsersCommand;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Application\Command\CommandHandlerInterface;
use CompanyOS\Domain\Shared\ValueObject\Uuid;
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