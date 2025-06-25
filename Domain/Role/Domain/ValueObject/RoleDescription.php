<?php

namespace CompanyOS\Domain\Role\Domain\ValueObject;

class RoleDescription
{
    private string $value;

    public function __construct(string $value)
    {
        if (strlen($value) > 1000) {
            throw new \InvalidArgumentException('Role description cannot be longer than 1000 characters');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(RoleDescription $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 