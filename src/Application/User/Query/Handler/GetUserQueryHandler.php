<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Query\Handler;

use CompanyOS\Bundle\CoreBundle\Application\User\Query\GetUserQuery;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepository;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandler;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

class GetUserQueryHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(GetUserQuery $query): ?array
    {
        $user = $this->userRepository->findById(Uuid::fromString($query->userId));
        
        if ($user === null) {
            return null;
        }

        return [
            'id' => $user->getId()->getValue(),
            'email' => $user->getEmail()->getValue(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'fullName' => $user->getFullName(),
            'isActive' => $user->isActive(),
            'createdAt' => $user->getCreatedAt(),
            'updatedAt' => $user->getUpdatedAt(),
        ];
    }
} 