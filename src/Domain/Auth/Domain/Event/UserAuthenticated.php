<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;

class UserAuthenticated extends DomainEvent
{
    public function __construct(
        private User $user,
        private string $ipAddress,
        private string $userAgent
    ) {
        parent::__construct();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getEventName(): string
    {
        return 'user.authenticated';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 