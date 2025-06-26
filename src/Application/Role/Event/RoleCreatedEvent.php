<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Event;

class RoleCreatedEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $name
    ) {}
} 