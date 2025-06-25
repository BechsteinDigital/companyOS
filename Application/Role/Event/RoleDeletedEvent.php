<?php

namespace CompanyOS\Domain\Role\Application\Event;

class RoleDeletedEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $name
    ) {}
} 