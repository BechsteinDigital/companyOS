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
        error_log('[OAuth2] getUserEntityByUserCredentials aufgerufen:');
        error_log('[OAuth2] - Username: ' . $username);
        error_log('[OAuth2] - GrantType: ' . $grantType);
        error_log('[OAuth2] - Client: ' . $clientEntity->getIdentifier());
        error_log('[OAuth2] - Password length: ' . strlen($password));
        
        // Nur für Password Grant
        if ($grantType !== 'password') {
            error_log('[OAuth2] Wrong grant type: ' . $grantType . ' - returning null');
            return null;
        }

        // User anhand E-Mail finden
        error_log('[OAuth2] Suche User mit E-Mail: ' . $username);
        $user = $this->userRepository->findByEmail(new \CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email($username));
        
        if (!$user) {
            error_log('[OAuth2] User nicht gefunden für E-Mail: ' . $username);
            return null;
        }
        
        error_log('[OAuth2] User gefunden: ID=' . $user->getId() . ', Email=' . $user->getEmail());
        
        if (!$user->isActive()) {
            error_log('[OAuth2] User ist nicht aktiv: ' . $username);
            return null;
        }

        // Passwort prüfen mit Symfony PasswordHasher
        if (!$user->getPasswordHash()) {
            error_log('[OAuth2] User hat kein Passwort-Hash: ' . $username);
            return null;
        }

        error_log('[OAuth2] Prüfe Passwort für User: ' . $username);
        error_log('[OAuth2] - Password Hash vorhanden: ' . (strlen($user->getPasswordHash()) > 0 ? 'ja' : 'nein'));
        
        if ($this->passwordHasher->isPasswordValid($user, $password)) {
            error_log('[OAuth2] Passwort ist korrekt für User: ' . $username);
            return $user;
        }

        error_log('[OAuth2] Passwort ist falsch für User: ' . $username);
        return null;
    }
} 