<?php

namespace CompanyOS\Application\Auth\Command;

use CompanyOS\Application\Command\CommandInterface;

class RequestPasswordResetCommand implements CommandInterface
{
    public function __construct(
        public readonly string $emailOrUsername
    ) {
    }
} 