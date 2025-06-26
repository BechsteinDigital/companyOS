<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;

class RequestPasswordResetCommand implements CommandInterface
{
    public function __construct(
        public readonly string $emailOrUsername
    ) {
    }
} 