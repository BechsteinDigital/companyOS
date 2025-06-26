<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Settings\EventSubscriber;

use CompanyOS\Bundle\CoreBundle\Application\Settings\Service\SettingsService;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventOccurred;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SettingsEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SettingsService $settingsService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DomainEventOccurred::class => 'onDomainEventOccurred',
        ];
    }

    public function onDomainEventOccurred(DomainEventOccurred $event): void
    {
        // Clear settings cache when domain events occur
        // This ensures that any settings changes are immediately reflected
        $this->settingsService->clearCache();
    }
} 