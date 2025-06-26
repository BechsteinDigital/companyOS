<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\Persistence;

use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Entity\Plugin;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Repository\PluginRepository;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

class DoctrinePluginRepository implements PluginRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(Plugin $plugin): void
    {
        $this->entityManager->persist($plugin);
        $this->entityManager->flush();
    }

    public function findById(Uuid $id): ?Plugin
    {
        return $this->entityManager->getRepository(Plugin::class)->find($id);
    }

    public function findByName(string $name): ?Plugin
    {
        return $this->entityManager->getRepository(Plugin::class)->findOneBy([
            'name' => $name
        ]);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Plugin::class)->findAll();
    }

    public function findActive(): array
    {
        return $this->entityManager->getRepository(Plugin::class)->findBy([
            'active' => true
        ]);
    }

    public function delete(Plugin $plugin): void
    {
        $this->entityManager->remove($plugin);
        $this->entityManager->flush();
    }

    public function existsByName(string $name): bool
    {
        $count = $this->entityManager->getRepository(Plugin::class)->count([
            'name' => $name
        ]);
        
        return $count > 0;
    }
} 