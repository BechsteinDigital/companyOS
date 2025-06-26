<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Event;

class RoleDeletedEvent
{
    public function __construct(
        public readonly string $roleId,
        public readonly string $name
    ) {}
} 