<?php

namespace CompanyOS\Domain\User\Domain\Event;

use CompanyOS\Domain\Shared\Event\DomainEvent;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

class UserDeleted extends DomainEvent
{
    public function getEventName(): string
    {
        return 'user.deleted';
    }

    public function getEventVersion(): string
    {
        return '1.0';
    }
} 