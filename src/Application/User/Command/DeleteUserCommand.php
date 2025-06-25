<?php

namespace CompanyOS\Domain\User\Application\Command;

use CompanyOS\Application\Command\Command;

class DeleteUserCommand implements Command
{
    public function __construct(
        public readonly string $userId
    ) {
    }
} 