<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\Command;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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