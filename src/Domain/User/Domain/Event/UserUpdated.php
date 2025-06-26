<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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