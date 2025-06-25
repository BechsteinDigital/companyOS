<?php

namespace CompanyOS\Application\User\Command;

use CompanyOS\Application\Command\Command;
use CompanyOS\Domain\ValueObject\Email;

class CreateUserCommand implements Command
{
    public function __construct(
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $password = null,
        public readonly array $roleIds = []
    ) {
    }
} 