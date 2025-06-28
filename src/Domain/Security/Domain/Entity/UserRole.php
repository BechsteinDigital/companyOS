<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Domain\Security\Domain\Entity;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_roles')]
#[ORM\UniqueConstraint(name: 'user_role_unique', columns: ['user_id', 'role_id'])]
#[ORM\Index(name: 'idx_user_roles_user', columns: ['user_id'])]
#[ORM\Index(name: 'idx_user_roles_role', columns: ['role_id'])]
class UserRole
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'uuid')]
    private Uuid $userId;

    #[ORM\Column(type: 'uuid')]
    private Uuid $roleId;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?Uuid $assignedBy = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $assignmentReason = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    // Lazy loading relationships
    #[ORM\ManyToOne(targetEntity: 'CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $user;

    #[ORM\ManyToOne(targetEntity: 'CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\Role')]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $role;

    public function __construct(
        Uuid $id,
        Uuid $userId,
        Uuid $roleId,
        ?Uuid $assignedBy = null,
        ?\DateTimeImmutable $expiresAt = null,
        ?string $assignmentReason = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->roleId = $roleId;
        $this->assignedBy = $assignedBy;
        $this->expiresAt = $expiresAt;
        $this->assignmentReason = $assignmentReason;
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

    public function getRoleId(): Uuid
    {
        return $this->roleId;
    }

    public function getAssignedBy(): ?Uuid
    {
        return $this->assignedBy;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getAssignmentReason(): ?string
    {
        return $this->assignmentReason;
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
        $this->assignmentReason = $reason;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getRole()
    {
        return $this->role;
    }
} 