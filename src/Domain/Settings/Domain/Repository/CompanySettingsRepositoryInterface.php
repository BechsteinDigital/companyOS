<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Repository;

use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Entity\CompanySettings;

interface CompanySettingsRepositoryInterface
{
    public function find(): ?CompanySettings;
    
    public function save(CompanySettings $settings): void;
    
    public function exists(): bool;
} 