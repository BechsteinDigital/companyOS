<?php

declare(strict_types=1);

namespace CompanyOS\Infrastructure\Auth\Event;

use CompanyOS\Domain\Auth\Domain\Event\UserAuthenticated;
use CompanyOS\Domain\Auth\Domain\Event\UserLoginFailed;
use CompanyOS\Domain\Auth\Domain\Event\UserLoggedOut;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;
use CompanyOS\Infrastructure\Event\DomainEventOccurred;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AuthenticationEventDispatcher implements DomainEventDispatcher
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger
    ) {
    }

    public function dispatch(DomainEventOccurred $event): void
    {
        try {
            $this->eventDispatcher->dispatch($event->getDomainEvent());
            
            $this->logger->debug('Domain event dispatched', [
                'event' => get_class($event->getDomainEvent()),
                'occurredAt' => $event->getOccurredAt()->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to dispatch domain event', [
                'event' => get_class($event->getDomainEvent()),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    public function dispatchUserAuthenticated(UserAuthenticated $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }

    public function dispatchUserLoginFailed(UserLoginFailed $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }

    public function dispatchUserLoggedOut(UserLoggedOut $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
} 