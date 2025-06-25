<?php

namespace CompanyOS\Domain\Auth\Application\Command;

use CompanyOS\Application\Command\CommandInterface;

class RefreshTokenCommand implements CommandInterface
{
    public function __construct(
        public readonly string $refreshToken,
        public readonly string $ipAddress = '',
        public readonly string $userAgent = ''
    ) {
    }
} 