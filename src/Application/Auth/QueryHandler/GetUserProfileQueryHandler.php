<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\QueryHandler;

use CompanyOS\Application\Auth\Query\GetUserProfileQuery;
use CompanyOS\Application\Auth\DTO\UserProfileResponse;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Application\Query\QueryHandler;
use CompanyOS\Application\Query\QueryHandlerInterface;

final class GetUserProfileQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(GetUserProfileQuery $query): UserProfileResponse
    {
        $user = $this->userRepository->findById($query->getUserId());
        
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        return new UserProfileResponse(
            id: $user->getId()->value(),
            email: $user->getEmail()->value(),
            firstName: $user->getFirstName()->value(),
            lastName: $user->getLastName()->value(),
            isActive: $user->isActive(),
            createdAt: $user->getCreatedAt()->format('Y-m-d H:i:s'),
            lastLoginAt: $user->getLastLoginAt()?->format('Y-m-d H:i:s')
        );
    }
} 