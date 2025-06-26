<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\Command;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email;

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