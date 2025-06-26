<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Repository;

use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Entity\Webhook;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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