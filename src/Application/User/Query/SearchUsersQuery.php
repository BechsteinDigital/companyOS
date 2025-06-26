<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

class SearchUsersQuery implements Query
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?array $roleIds = null,
        public readonly ?bool $isActive = null,
        public readonly ?string $email = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $sortBy = 'createdAt',
        public readonly string $sortOrder = 'DESC',
        public readonly int $limit = 50,
        public readonly int $offset = 0
    ) {
    }
} 