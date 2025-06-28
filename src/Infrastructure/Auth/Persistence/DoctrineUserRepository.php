<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;

class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private LoggerInterface $logger
    ) {
    }

    public function getUserEntityByUserCredentials(
        string $username,
        string $password,
        string $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        // Debug-Logging
        $this->logger->info('[OAuth2] getUserEntityByUserCredentials aufgerufen', [
            'username' => $username,
            'grantType' => $grantType,
            'client' => $clientEntity->getIdentifier(),
            'passwordLength' => strlen($password),
        ]);
        
        // Nur für Password Grant
        if ($grantType !== 'password') {
            $this->logger->warning('[OAuth2] Wrong grant type', ['grantType' => $grantType]);
            return null;
        }

        // Input-Sanitization und Validierung
        $sanitizedUsername = $this->sanitizeEmail($username);
        if (!$this->validateEmail($sanitizedUsername)) {
            $this->logger->warning('[OAuth2] Invalid email format', ['email' => $username]);
            return null;
        }

        // User anhand E-Mail finden
        $this->logger->info('[OAuth2] Suche User mit E-Mail', ['email' => $sanitizedUsername]);
        
        try {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $sanitizedUsername]);
            
            if (!$user) {
                $this->logger->warning('[OAuth2] User nicht gefunden für E-Mail', ['email' => $sanitizedUsername]);
                return null;
            }
            
            $this->logger->info('[OAuth2] User gefunden', [
                'id' => $user->getId()->getValue(),
                'email' => $user->getEmail()->getValue(),
                'isActive' => $user->isActive(),
                'hasPassword' => $user->getPassword() !== null
            ]);
            
            if (!$user->isActive()) {
                $this->logger->warning('[OAuth2] User ist nicht aktiv', ['email' => $sanitizedUsername]);
                return null;
            }

            // Passwort prüfen mit Symfony PasswordHasher
            if (!$user->getPassword()) {
                $this->logger->warning('[OAuth2] User hat kein Passwort-Hash', ['email' => $sanitizedUsername]);
                return null;
            }

            $isPasswordValid = $this->passwordHasher->isPasswordValid($user, $password);
            $this->logger->info('[OAuth2] Password validation result', [
                'email' => $sanitizedUsername,
                'isValid' => $isPasswordValid
            ]);
            
            if ($isPasswordValid) {
                $this->logger->info('[OAuth2] Passwort ist korrekt für User', ['email' => $sanitizedUsername]);
                // Direkt die User-Entity zurückgeben, da sie bereits UserEntityInterface implementiert
                return $user;
            }

            $this->logger->warning('[OAuth2] Passwort ist falsch für User', ['email' => $sanitizedUsername]);
            return null;
            
        } catch (\Exception $e) {
            $this->logger->error('[OAuth2] Exception in getUserEntityByUserCredentials', [
                'email' => $sanitizedUsername,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Sanitize email input
     */
    private function sanitizeEmail(string $email): string
    {
        return trim(strtolower($email));
    }

    /**
     * Validate email format
     */
    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
} 