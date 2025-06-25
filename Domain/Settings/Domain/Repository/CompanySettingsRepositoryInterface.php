<?php

namespace CompanyOS\Domain\Settings\Domain\Repository;

use CompanyOS\Domain\Settings\Domain\Entity\CompanySettings;

interface CompanySettingsRepositoryInterface
{
    public function find(): ?CompanySettings;
    
    public function save(CompanySettings $settings): void;
    
    public function exists(): bool;
} 