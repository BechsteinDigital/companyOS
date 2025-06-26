<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Event;

class RoleRemovedFromUserEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $userId
    ) {}
} 