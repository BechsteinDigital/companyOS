<?php

namespace CompanyOS\Domain\Plugin\Domain\Repository;

use CompanyOS\Domain\Plugin\Domain\Entity\Plugin;
use CompanyOS\Domain\ValueObject\Uuid;

interface PluginRepository
{
    public function save(Plugin $plugin): void;
    public function findById(Uuid $id): ?Plugin;
    public function findByName(string $name): ?Plugin;
    public function findAll(): array;
    public function findActive(): array;
    public function delete(Plugin $plugin): void;
    public function existsByName(string $name): bool;
} 