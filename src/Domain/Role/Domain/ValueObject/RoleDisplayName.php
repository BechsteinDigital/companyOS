<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject;

class RoleDisplayName
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Role display name cannot be empty');
        }
        if (strlen($value) > 255) {
            throw new \InvalidArgumentException('Role display name cannot be longer than 255 characters');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(RoleDisplayName $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 