<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\Role;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;

class RoleAssignedToUser extends DomainEvent
{
    public function __construct(
        private Role $role,
        private User $user
    ) {
        parent::__construct();
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEventName(): string
    {
        return 'role.assigned_to_user';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 