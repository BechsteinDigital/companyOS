<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject;

class RolePermissions
{
    private array $value;

    public function __construct(array $value)
    {
        foreach ($value as $permission) {
            if (empty($permission)) {
                throw new \InvalidArgumentException('Permission cannot be empty');
            }
            if (strlen($permission) > 255) {
                throw new \InvalidArgumentException('Permission cannot be longer than 255 characters');
            }
        }
        $this->value = array_unique($value);
    }

    public function value(): array
    {
        return $this->value;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->value, true);
    }

    public function addPermission(string $permission): void
    {
        if (empty($permission)) {
            throw new \InvalidArgumentException('Permission cannot be empty');
        }
        if (strlen($permission) > 255) {
            throw new \InvalidArgumentException('Permission cannot be longer than 255 characters');
        }
        if (!in_array($permission, $this->value, true)) {
            $this->value[] = $permission;
        }
    }

    public function removePermission(string $permission): void
    {
        $this->value = array_filter($this->value, fn($p) => $p !== $permission);
    }

    public function equals(RolePermissions $other): bool
    {
        return $this->value == $other->value;
    }

    public function count(): int
    {
        return count($this->value);
    }
} 