<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;

class LogoutUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $userId,
        public readonly string $ipAddress = '',
        public readonly string $userAgent = ''
    ) {
    }
} 