<?php

namespace CompanyOS\Domain\Auth\Application\Command;

use CompanyOS\Application\Command\CommandInterface;

class ResetPasswordCommand implements CommandInterface
{
    public function __construct(
        public readonly string $resetToken,
        public readonly string $newPassword
    ) {
    }
} 