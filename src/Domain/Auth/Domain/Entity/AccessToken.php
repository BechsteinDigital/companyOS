<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

#[ORM\Entity]
#[ORM\Table(name: 'oauth_access_tokens')]
class AccessToken implements AccessTokenEntityInterface
{
    use EntityTrait;
    use TokenEntityTrait;
    use AccessTokenTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'boolean')]
    private bool $isRevoked = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'uuid')]
    private Uuid $userId;

    #[ORM\Column(type: 'uuid')]
    private Uuid $clientId;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return Uuid::fromString((string) $this->id);
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function setUserId(Uuid $userId): void
    {
        $this->userId = $userId;
    }

    public function getClientId(): Uuid
    {
        return $this->clientId;
    }

    public function setClientId(Uuid $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getScopes(): array
    {
        return $this->scopes ?? [];
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiryDateTime;
    }

    public function isExpired(): bool
    {
        return $this->expiryDateTime < new \DateTimeImmutable();
    }

    public function isRevoked(): bool
    {
        return $this->isRevoked;
    }

    public function setIsRevoked(bool $isRevoked): void
    {
        $this->isRevoked = $isRevoked;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
} 