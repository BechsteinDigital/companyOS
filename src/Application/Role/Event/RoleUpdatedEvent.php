<?php

namespace CompanyOS\Application\Role\Event;

class RoleUpdatedEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $name
    ) {}
} 