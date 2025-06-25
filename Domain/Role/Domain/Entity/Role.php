<?php

namespace CompanyOS\Domain\Role\Domain\Entity;

use CompanyOS\Domain\Role\Domain\ValueObject\RoleId;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleName;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleDisplayName;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleDescription;
use CompanyOS\Domain\Role\Domain\ValueObject\RolePermissions;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'roles')]
#[ORM\HasLifecycleCallbacks]
class Role
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $displayName;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'json')]
    private array $permissions;

    #[ORM\Column(type: 'boolean')]
    private bool $isSystem;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        RoleName $name,
        RoleDisplayName $displayName,
        ?RoleDescription $description = null,
        RolePermissions $permissions = null,
        bool $isSystem = false
    ) {
        $this->id = Uuid::v4()->toRfc4122();
        $this->name = $name->value();
        $this->displayName = $displayName->value();
        $this->description = $description?->value();
        $this->permissions = $permissions?->value() ?? [];
        $this->isSystem = $isSystem;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function id(): RoleId
    {
        return new RoleId($this->id);
    }

    public function name(): RoleName
    {
        return new RoleName($this->name);
    }

    public function displayName(): RoleDisplayName
    {
        return new RoleDisplayName($this->displayName);
    }

    public function description(): ?RoleDescription
    {
        return $this->description ? new RoleDescription($this->description) : null;
    }

    public function permissions(): RolePermissions
    {
        return new RolePermissions($this->permissions);
    }

    public function isSystem(): bool
    {
        return $this->isSystem;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateDisplayName(RoleDisplayName $displayName): void
    {
        $this->displayName = $displayName->value();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateDescription(?RoleDescription $description): void
    {
        $this->description = $description?->value();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updatePermissions(RolePermissions $permissions): void
    {
        $this->permissions = $permissions->value();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function canBeDeleted(): bool
    {
        return !$this->isSystem;
    }
} 