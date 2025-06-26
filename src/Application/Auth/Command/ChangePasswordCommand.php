<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;

class ChangePasswordCommand implements CommandInterface
{
    public function __construct(
        public readonly string $userId,
        public readonly string $oldPassword,
        public readonly string $newPassword
    ) {
    }
} 