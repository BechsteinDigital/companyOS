<?php

namespace CompanyOS\Infrastructure\Settings\Persistence;

use CompanyOS\Domain\Settings\Domain\Entity\CompanySettings;
use CompanyOS\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCompanySettingsRepository implements CompanySettingsRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function find(): ?CompanySettings
    {
        return $this->entityManager->getRepository(CompanySettings::class)->findOneBy([]);
    }

    public function save(CompanySettings $settings): void
    {
        $this->entityManager->persist($settings);
        $this->entityManager->flush();
    }

    public function exists(): bool
    {
        $count = $this->entityManager->getRepository(CompanySettings::class)->count([]);
        return $count > 0;
    }
} 