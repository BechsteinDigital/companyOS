<?php

namespace CompanyOS\Domain\Auth\Application\Command;

use CompanyOS\Application\Command\CommandInterface;

class LogoutUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $userId,
        public readonly string $ipAddress = '',
        public readonly string $userAgent = ''
    ) {
    }
} 