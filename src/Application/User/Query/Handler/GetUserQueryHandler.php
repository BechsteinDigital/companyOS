<?php

namespace CompanyOS\Domain\User\Application\Query\Handler;

use CompanyOS\Domain\User\Application\Query\GetUserQuery;
use CompanyOS\Domain\User\Domain\Repository\UserRepository;
use CompanyOS\Application\Query\QueryHandler;
use CompanyOS\Domain\ValueObject\Uuid;

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
            'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
} 