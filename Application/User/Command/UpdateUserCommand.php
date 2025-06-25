<?php

namespace CompanyOS\Domain\User\Application\Command;

use CompanyOS\Application\Command\Command;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

class UpdateUserCommand implements Command
{
    public function __construct(
        public readonly string $userId,
        public readonly ?string $email = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?array $roleIds = null
    ) {
    }
} 