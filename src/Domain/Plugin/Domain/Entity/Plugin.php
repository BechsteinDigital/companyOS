<?php

namespace CompanyOS\Domain\Plugin\Domain\Entity;

use CompanyOS\Domain\Plugin\Domain\Event\PluginInstalled;
use CompanyOS\Domain\Plugin\Domain\Event\PluginActivated;
use CompanyOS\Domain\Plugin\Domain\Event\PluginDeactivated;
use CompanyOS\Domain\Plugin\Domain\Event\PluginUpdated;
use CompanyOS\Domain\Plugin\Domain\Event\PluginDeleted;
use CompanyOS\Domain\Event\DomainEvent;
use CompanyOS\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'plugins')]
class Plugin
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $version;

    #[ORM\Column(type: 'string', length: 255)]
    private string $author;

    #[ORM\Column(type: 'boolean')]
    private bool $active;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $meta;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    private array $domainEvents = [];

    public function __construct(
        Uuid $id,
        string $name,
        string $version,
        string $author,
        ?array $meta = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->version = $version;
        $this->author = $author;
        $this->active = false;
        $this->meta = $meta;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->addDomainEvent(new PluginInstalled($id));
    }

    public function getId(): Uuid { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getVersion(): string { return $this->version; }
    public function getAuthor(): string { return $this->author; }
    public function isActive(): bool { return $this->active; }
    public function getMeta(): ?array { return $this->meta; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function activate(): void {
        if (!$this->active) {
            $this->active = true;
            $this->updatedAt = new \DateTimeImmutable();
            $this->addDomainEvent(new PluginActivated($this->id));
        }
    }
    public function deactivate(): void {
        if ($this->active) {
            $this->active = false;
            $this->updatedAt = new \DateTimeImmutable();
            $this->addDomainEvent(new PluginDeactivated($this->id));
        }
    }
    public function update(string $version, ?array $meta = null): void {
        $this->version = $version;
        $this->meta = $meta;
        $this->updatedAt = new \DateTimeImmutable();
        $this->addDomainEvent(new PluginUpdated($this->id));
    }
    public function delete(): void {
        $this->addDomainEvent(new PluginDeleted($this->id));
    }
    private function addDomainEvent(DomainEvent $event): void {
        $this->domainEvents[] = $event;
    }
    public function getDomainEvents(): array {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    public function updateVersion(string $newVersion): void
    {
        $this->version = $newVersion;
        $this->updatedAt = new \DateTimeImmutable();
    }
} 