<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\Command;

class BulkUpdateUsersCommand implements Command
{
    public function __construct(
        public readonly array $userIds,
        public readonly ?array $roleIds = null,
        public readonly ?bool $isActive = null
    ) {
    }
} 