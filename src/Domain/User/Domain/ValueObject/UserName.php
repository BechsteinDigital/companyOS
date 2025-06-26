<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\User\Domain\ValueObject;

class UserName
{
    public function __construct(
        private string $value
    ) {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Username cannot be empty');
        }
        
        if (strlen($value) > 255) {
            throw new \InvalidArgumentException('Username cannot be longer than 255 characters');
        }
        
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $value)) {
            throw new \InvalidArgumentException('Username contains invalid characters');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 