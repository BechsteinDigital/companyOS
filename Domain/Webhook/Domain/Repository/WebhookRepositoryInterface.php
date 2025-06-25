<?php

namespace CompanyOS\Domain\Webhook\Domain\Repository;

use CompanyOS\Domain\Webhook\Domain\Entity\Webhook;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

interface WebhookRepositoryInterface
{
    public function save(Webhook $webhook): void;

    public function findById(Uuid $id): ?Webhook;

    public function findByName(string $name): ?Webhook;

    public function findByUrl(string $url): ?Webhook;

    public function findAll(): array;

    public function findByEvent(string $eventName): array;

    public function findActive(): array;

    public function delete(Webhook $webhook): void;
} 