<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Plugin\Application\DTO;

final class PluginDependencyResponse
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly bool $isInstalled,
        public readonly bool $isActive,
        public readonly ?string $currentVersion
    ) {
    }
} 