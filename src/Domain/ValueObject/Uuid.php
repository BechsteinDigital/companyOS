<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\ValueObject;

class Uuid
{
    private string $value;

    public function __construct(string $value = null)
    {
        if ($value === null) {
            $value = self::generateV4();
        }
        if (!self::isValid($value)) {
            throw new \InvalidArgumentException('Invalid UUID: ' . $value);
        }
        $this->value = $value;
    }

    public static function random(): self
    {
        return new self(self::generateV4());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public static function isValid(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value) === 1;
    }

    private static function generateV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
} 