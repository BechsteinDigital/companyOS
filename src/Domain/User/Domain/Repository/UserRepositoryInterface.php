<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(Uuid $id): ?User;

    public function findByEmail(Email $email): ?User;

    public function findAll(): array;

    public function findActive(): array;

    public function delete(User $user): void;

    public function existsByEmail(Email $email): bool;
} 