<?php

namespace CompanyOS\Application\User\Command;

use CompanyOS\Application\Command\Command;

class BulkDeleteUsersCommand implements Command
{
    public function __construct(
        public readonly array $userIds,
        public readonly bool $force = false
    ) {
    }
} 