<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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