<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\User\Domain\ValueObject;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

class UserId extends Uuid
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }
} 