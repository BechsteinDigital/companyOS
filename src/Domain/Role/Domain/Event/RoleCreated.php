<?php

namespace CompanyOS\Domain\Role\Domain\Event;

use CompanyOS\Domain\Role\Domain\Entity\Role;
use CompanyOS\Domain\Event\DomainEvent;

class RoleCreated extends DomainEvent
{
    public function __construct(
        private Role $role
    ) {
        parent::__construct();
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getEventName(): string
    {
        return 'role.created';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 