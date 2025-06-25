<?php

namespace CompanyOS\Domain\Auth\Application\Command;

use CompanyOS\Application\Command\CommandInterface;

class RequestPasswordResetCommand implements CommandInterface
{
    public function __construct(
        public readonly string $emailOrUsername
    ) {
    }
} 