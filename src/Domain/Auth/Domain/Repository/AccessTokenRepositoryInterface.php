<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Repository;

use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity\AccessToken;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

interface AccessTokenRepositoryInterface
{
    public function save(AccessToken $accessToken): void;
    
    public function findById(Uuid $id): ?AccessToken;
    
    public function findByToken(string $token): ?AccessToken;
    
    public function findActiveByUserId(Uuid $userId): array;
    
    public function delete(AccessToken $accessToken): void;
    
    public function deleteExpired(): void;
} 