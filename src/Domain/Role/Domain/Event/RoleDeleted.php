<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\Role;
use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;

class RoleDeleted extends DomainEvent
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
        return 'role.deleted';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 