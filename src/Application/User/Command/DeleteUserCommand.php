<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\Command;

class DeleteUserCommand implements Command
{
    public function __construct(
        public readonly string $userId
    ) {
    }
} 