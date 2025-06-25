<?php

namespace CompanyOS\Domain\User\Domain\Event;

use CompanyOS\Domain\Event\DomainEvent;
use CompanyOS\Domain\ValueObject\Uuid;

class UserCreated extends DomainEvent
{
    public function getEventName(): string
    {
        return 'user.created';
    }

    public function getEventVersion(): string
    {
        return '1.0';
    }
} 