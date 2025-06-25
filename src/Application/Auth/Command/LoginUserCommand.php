<?php

namespace CompanyOS\Application\Auth\Command;

use CompanyOS\Application\Command\CommandInterface;

class LoginUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $usernameOrEmail,
        public readonly string $password,
        public readonly string $ipAddress = '',
        public readonly string $userAgent = ''
    ) {
    }
} 