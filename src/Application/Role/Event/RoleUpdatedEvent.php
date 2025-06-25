<?php

namespace CompanyOS\Domain\Role\Application\Event;

class RoleUpdatedEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $name
    ) {}
} 