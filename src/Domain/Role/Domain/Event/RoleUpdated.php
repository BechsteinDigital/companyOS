<?php

namespace CompanyOS\Domain\Role\Domain\Event;

use CompanyOS\Domain\Role\Domain\Entity\Role;
use CompanyOS\Domain\Event\DomainEvent;

class RoleUpdated extends DomainEvent
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
        return 'role.updated';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 