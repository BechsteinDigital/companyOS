<?php

namespace CompanyOS\Application\Role\Event;

class RoleCreatedEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $name
    ) {}
} 