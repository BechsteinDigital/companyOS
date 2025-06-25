<?php

namespace CompanyOS\Domain\Role\Application\Event;

class RoleCreatedEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $name
    ) {}
} 