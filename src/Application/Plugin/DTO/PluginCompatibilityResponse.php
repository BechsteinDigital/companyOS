<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Plugin\Application\DTO;

final class PluginCompatibilityResponse
{
    public function __construct(
        public readonly bool $isCompatible,
        public readonly array $issues,
        public readonly array $warnings,
        public readonly array $recommendations,
        public readonly array $systemInfo
    ) {
    }
} 