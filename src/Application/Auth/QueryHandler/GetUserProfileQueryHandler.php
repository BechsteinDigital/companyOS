<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Auth\Query\GetUserProfileQuery;
use CompanyOS\Bundle\CoreBundle\Application\Auth\DTO\UserProfileResponse;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandlerInterface;

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