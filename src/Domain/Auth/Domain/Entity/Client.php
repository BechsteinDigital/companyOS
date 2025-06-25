<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Domain\Entity;

use CompanyOS\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

#[ORM\Entity]
#[ORM\Table(name: 'oauth_clients')]
class Client implements ClientEntityInterface
{
    use EntityTrait;
    use ClientTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $clientId;

    #[ORM\Column(type: 'string')]
    private string $clientName;

    #[ORM\Column(type: 'json')]
    private array $redirectUris = [];

    #[ORM\Column(type: 'json')]
    private array $scopes = [];

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return Uuid::fromString((string) $this->id);
    }

    public function getClientId(): Uuid
    {
        return Uuid::fromString($this->clientId);
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): void
    {
        $this->clientName = $clientName;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getRedirectUris(): array
    {
        return $this->redirectUris;
    }

    public function setRedirectUris(array $redirectUris): void
    {
        $this->redirectUris = $redirectUris;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
} 