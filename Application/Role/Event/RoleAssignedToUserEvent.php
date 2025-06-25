<?php

namespace CompanyOS\Domain\Role\Application\Event;

class RoleAssignedToUserEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $userId
    ) {}
} 