<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\User\Command\BulkUpdateUsersCommand;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class BulkUpdateUsersCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    public function __invoke(BulkUpdateUsersCommand $command): void
    {
        $updatedCount = 0;
        $errors = [];

        foreach ($command->userIds as $userId) {
            try {
                $user = $this->userRepository->findById(Uuid::fromString($userId));
                
                if (!$user) {
                    $errors[] = "User with ID {$userId} not found";
                    continue;
                }

                // Update roles if provided
                if ($command->roleIds !== null) {
                    $roles = [];
                    foreach ($command->roleIds as $roleId) {
                        $role = $this->roleRepository->findById(Uuid::fromString($roleId));
                        if ($role) {
                            $roles[] = $role;
                        }
                    }
                    $user->setRoles($roles);
                }

                // Update active status if provided
                if ($command->isActive !== null) {
                    if ($command->isActive) {
                        $user->activate();
                    } else {
                        $user->deactivate();
                    }
                }

                $this->userRepository->save($user);
                $updatedCount++;
                
            } catch (\Exception $e) {
                $errors[] = "Failed to update user {$userId}: " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            throw new \RuntimeException(
                "Bulk update completed with errors. Updated: {$updatedCount}, Errors: " . implode(', ', $errors)
            );
        }
    }
} 