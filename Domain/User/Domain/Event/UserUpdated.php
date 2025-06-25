<?php

namespace CompanyOS\Domain\User\Domain\Event;

use CompanyOS\Domain\Shared\Event\DomainEvent;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

class UserUpdated extends DomainEvent
{
    public function getEventName(): string
    {
        return 'user.updated';
    }

    public function getEventVersion(): string
    {
        return '1.0';
    }
} 