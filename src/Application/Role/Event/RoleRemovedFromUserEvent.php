<?php

namespace CompanyOS\Application\Role\Event;

class RoleRemovedFromUserEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $userId
    ) {}
} 