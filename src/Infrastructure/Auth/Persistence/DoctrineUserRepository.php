<?php

namespace CompanyOS\Infrastructure\Auth\Persistence;

use CompanyOS\Domain\User\Domain\Entity\User;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface as OAuthUserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DoctrineUserRepository implements OAuthUserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function getUserEntityByUserCredentials(
        string $username,
        string $password,
        string $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        // Nur für Password Grant
        if ($grantType !== 'password') {
            return null;
        }

        // User anhand E-Mail finden
        $user = $this->userRepository->findByEmail(new \CompanyOS\Domain\ValueObject\Email($username));
        
        if (!$user || !$user->isActive()) {
            return null;
        }

        // Passwort prüfen mit Symfony PasswordHasher
        if (!$user->getPasswordHash()) {
            return null;
        }

        if ($this->passwordHasher->isPasswordValid($user, $password)) {
            return $user;
        }

        return null;
    }
} 