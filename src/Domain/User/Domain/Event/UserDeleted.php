<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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