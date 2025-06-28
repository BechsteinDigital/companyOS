<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Domain\Security\Domain\Entity;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'access_control_entries')]
#[ORM\Index(name: 'idx_acl_user_resource', columns: ['user_id', 'resource_id', 'resource_type'])]
#[ORM\Index(name: 'idx_acl_resource', columns: ['resource_id', 'resource_type'])]
class AccessControlEntry
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'uuid')]
    private Uuid $userId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $resourceId;

    #[ORM\Column(type: 'string', length: 100)]
    private string $resourceType;

    #[ORM\Column(type: 'string', length: 100)]
    private string $permission;

    #[ORM\Column(type: 'string', length: 20)]
    private string $type; // 'allow' or 'deny'

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?Uuid $grantedBy = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        Uuid $userId,
        string $resourceId,
        string $resourceType,
        string $permission,
        string $type,
        ?Uuid $grantedBy = null,
        ?string $reason = null,
        ?\DateTimeImmutable $expiresAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->resourceId = $resourceId;
        $this->resourceType = $resourceType;
        $this->permission = $permission;
        $this->type = $type;
        $this->grantedBy = $grantedBy;
        $this->reason = $reason;
        $this->expiresAt = $expiresAt;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getPermission(): string
    {
        return $this->permission;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isAllow(): bool
    {
        return $this->type === 'allow';
    }

    public function isDeny(): bool
    {
        return $this->type === 'deny';
    }

    public function getGrantedBy(): ?Uuid
    {
        return $this->grantedBy;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt !== null && $this->expiresAt < new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateExpiration(?\DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateReason(?string $reason): void
    {
        $this->reason = $reason;
        $this->updatedAt = new \DateTimeImmutable();
    }
} 