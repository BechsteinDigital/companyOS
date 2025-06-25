<?php

namespace CompanyOS\Domain\Plugin\Domain\ValueObject;

enum PluginStatus: string
{
    case INSTALLED = 'installed';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ERROR = 'error';
    case UPDATING = 'updating';

    public function getLabel(): string
    {
        return match($this) {
            self::INSTALLED => 'Installiert',
            self::ACTIVE => 'Aktiv',
            self::INACTIVE => 'Inaktiv',
            self::ERROR => 'Fehler',
            self::UPDATING => 'Update läuft',
        };
    }

    public function canActivate(): bool
    {
        return in_array($this, [self::INSTALLED, self::INACTIVE]);
    }

    public function canDeactivate(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canUpdate(): bool
    {
        return in_array($this, [self::ACTIVE, self::INACTIVE]);
    }

    public function canDelete(): bool
    {
        return in_array($this, [self::INSTALLED, self::INACTIVE, self::ERROR]);
    }
} 