<?php

namespace CompanyOS\Domain\User\Domain\ValueObject;

use CompanyOS\Domain\ValueObject\Uuid;

class UserId extends Uuid
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }
} 