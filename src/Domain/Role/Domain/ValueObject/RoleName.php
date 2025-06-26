<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject;

class RoleName
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Role name cannot be empty');
        }
        if (strlen($value) > 100) {
            throw new \InvalidArgumentException('Role name cannot be longer than 100 characters');
        }
        if (!preg_match('/^[a-z0-9_-]+$/', $value)) {
            throw new \InvalidArgumentException('Role name can only contain lowercase letters, numbers, underscores and hyphens');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(RoleName $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 