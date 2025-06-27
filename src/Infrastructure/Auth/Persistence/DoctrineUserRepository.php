<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
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
        // Debug-Logging
        error_log("OAuth2 User Auth Debug:");
        error_log("Username: " . $username);
        error_log("GrantType: " . $grantType);
        error_log("Client: " . $clientEntity->getIdentifier());
        
        // Nur für Password Grant
        if ($grantType !== 'password') {
            error_log("Wrong grant type: " . $grantType);
            return null;
        }

        // User anhand E-Mail finden
        $user = $this->userRepository->findByEmail(new \CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email($username));
        
        if (!$user) {
            error_log("User not found for email: " . $username);
            return null;
        }
        
        if (!$user->isActive()) {
            error_log("User is not active: " . $username);
            return null;
        }

        // Passwort prüfen mit Symfony PasswordHasher
        if (!$user->getPasswordHash()) {
            error_log("User has no password hash: " . $username);
            return null;
        }

        error_log("Checking password for user: " . $username);
        if ($this->passwordHasher->isPasswordValid($user, $password)) {
            error_log("Password is valid for user: " . $username);
            return $user;
        }

        error_log("Password is invalid for user: " . $username);
        return null;
    }
} 