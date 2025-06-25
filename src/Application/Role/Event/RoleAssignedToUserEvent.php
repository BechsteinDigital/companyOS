<?php

namespace CompanyOS\Application\Role\Event;

class RoleAssignedToUserEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $userId
    ) {}
} 