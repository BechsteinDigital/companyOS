<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'roles')]
class Role
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $displayName;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isSystem = false;

    #[ORM\Column(type: 'json')]
    private array $permissions = [];

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToMany(targetEntity: 'CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User', mappedBy: 'roles')]
    private Collection $users;

    public function __construct(
        Uuid $id,
        string $name,
        string $displayName,
        ?string $description = null,
        bool $isSystem = false,
        array $permissions = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->displayName = $displayName;
        $this->description = $description;
        $this->isSystem = $isSystem;
        $this->permissions = $permissions;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->users = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isSystem(): bool
    {
        return $this->isSystem;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        // 1. Super-Admin: ** = alle Permissions
        if (in_array('**', $this->permissions, true)) {
            return true;
        }
        
        // 2. Exakte Permission-Übereinstimmung
        if (in_array($permission, $this->permissions, true)) {
            return true;
        }
        
        // 3. Wildcard-Permissions (z.B. user.* für user.create, user.read, etc.)
        $permissionParts = explode('.', $permission);
        if (count($permissionParts) >= 2) {
            $wildcardPermission = $permissionParts[0] . '.*';
            if (in_array($wildcardPermission, $this->permissions, true)) {
                return true;
            }
        }
        
        return false;
    }

    public function addPermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            $this->permissions[] = $permission;
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function removePermission(string $permission): void
    {
        $key = array_search($permission, $this->permissions, true);
        if ($key !== false) {
            unset($this->permissions[$key]);
            $this->permissions = array_values($this->permissions);
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function updateDetails(string $displayName, ?string $description = null): void
    {
        $this->displayName = $displayName;
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getUsers(): Collection
    {
        return $this->users;
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