<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Domain\Repository;

use CompanyOS\Domain\Auth\Domain\Entity\Client;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

interface ClientRepositoryInterface
{
    public function save(Client $client): void;
    
    public function findById(Uuid $id): ?Client;
    
    public function findByClientId(string $clientId): ?Client;
    
    public function findAll(?string $clientId = null, ?string $clientName = null): array;
    
    public function delete(Client $client): void;
} 