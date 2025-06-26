<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;

class UserLoginFailed extends DomainEvent
{
    public function __construct(
        private string $username,
        private string $ipAddress,
        private string $userAgent,
        private string $reason
    ) {
        parent::__construct();
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getEventName(): string
    {
        return 'user.login_failed';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 