<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\User\Domain\ValueObject;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case PENDING = 'pending';

    public function getLabel(): string
    {
        return match($this) {
            self::ACTIVE => 'Aktiv',
            self::INACTIVE => 'Inaktiv',
            self::SUSPENDED => 'Gesperrt',
            self::PENDING => 'Ausstehend',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canLogin(): bool
    {
        return in_array($this, [self::ACTIVE, self::PENDING]);
    }
} 