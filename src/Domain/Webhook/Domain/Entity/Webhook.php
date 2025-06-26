<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Entity;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'webhooks')]
class Webhook
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $url;

    #[ORM\Column(type: 'json')]
    private array $events;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $secret;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        string $name,
        string $url,
        array $events,
        ?string $secret = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->events = $events;
        $this->secret = $secret;
        $this->isActive = true;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function update(string $name, string $url, array $events, ?string $secret = null): void
    {
        $this->name = $name;
        $this->url = $url;
        $this->events = $events;
        $this->secret = $secret;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function shouldReceiveEvent(string $eventName): bool
    {
        return $this->isActive && in_array($eventName, $this->events, true);
    }

    public function generateSignature(string $payload): string
    {
        if (!$this->secret) {
            return '';
        }

        return hash_hmac('sha256', $payload, $this->secret);
    }
} 