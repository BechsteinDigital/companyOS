<?php

namespace CompanyOS\Domain\Settings\Domain\Event;

use CompanyOS\Domain\Settings\Domain\Entity\CompanySettings;
use CompanyOS\Domain\Shared\Event\DomainEvent;

class SettingsInitialized extends DomainEvent
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
        return 'settings.initialized';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 