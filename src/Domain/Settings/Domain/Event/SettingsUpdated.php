<?php

namespace CompanyOS\Domain\Settings\Domain\Event;

use CompanyOS\Domain\Settings\Domain\Entity\CompanySettings;
use CompanyOS\Domain\Event\DomainEvent;

class SettingsUpdated extends DomainEvent
{
    public function __construct(
        private CompanySettings $settings
    ) {
        parent::__construct();
    }

    public function getSettings(): CompanySettings
    {
        return $this->settings;
    }

    public function getEventName(): string
    {
        return 'settings.updated';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 