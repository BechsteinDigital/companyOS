<?php

namespace CompanyOS\Domain\Auth\Application\Command;

use CompanyOS\Application\Command\CommandInterface;

class ChangePasswordCommand implements CommandInterface
{
    public function __construct(
        public readonly string $userId,
        public readonly string $oldPassword,
        public readonly string $newPassword
    ) {
    }
} 