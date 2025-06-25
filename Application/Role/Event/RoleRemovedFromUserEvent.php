<?php

namespace CompanyOS\Domain\Role\Application\Event;

class RoleRemovedFromUserEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $userId
    ) {}
} 