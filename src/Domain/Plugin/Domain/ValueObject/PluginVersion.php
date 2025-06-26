<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\ValueObject;

class PluginVersion
{
    public function __construct(
        private string $version
    ) {
        if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            throw new \InvalidArgumentException('Invalid version format. Expected format: x.y.z');
        }
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function isGreaterThan(PluginVersion $other): bool
    {
        return version_compare($this->version, $other->version, '>');
    }

    public function isLessThan(PluginVersion $other): bool
    {
        return version_compare($this->version, $other->version, '<');
    }

    public function equals(PluginVersion $other): bool
    {
        return $this->version === $other->version;
    }

    public function __toString(): string
    {
        return $this->version;
    }
} 