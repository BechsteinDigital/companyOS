<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\Command;

class BulkDeleteUsersCommand implements Command
{
    public function __construct(
        public readonly array $userIds,
        public readonly bool $force = false
    ) {
    }
} 