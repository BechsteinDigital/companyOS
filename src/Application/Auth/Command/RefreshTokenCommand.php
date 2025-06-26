<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;

class RefreshTokenCommand implements CommandInterface
{
    public function __construct(
        public readonly string $refreshToken,
        public readonly string $ipAddress = '',
        public readonly string $userAgent = ''
    ) {
    }
} 