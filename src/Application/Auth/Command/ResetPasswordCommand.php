<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;

class ResetPasswordCommand implements CommandInterface
{
    public function __construct(
        public readonly string $resetToken,
        public readonly string $newPassword
    ) {
    }
} 