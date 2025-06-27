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
        // Debug-Log beim Konstruktor
        $this->logger->info('[OAuth2] DoctrineUserRepository wurde instanziiert');
    }

    public function getUserEntityByUserCredentials(
        string $username,
        string $password,
        string $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        // Debug-Logging am Anfang der Methode
        $this->logger->info('[OAuth2] getUserEntityByUserCredentials aufgerufen', [
            'username' => $username,
            'grantType' => $grantType,
            'client' => $clientEntity->getIdentifier(),
            'passwordLength' => strlen($password),
            'method' => __METHOD__
        ]);
        
        // Nur für Password Grant
        if ($grantType !== 'password') {
            $this->logger->warning('[OAuth2] Wrong grant type', ['grantType' => $grantType]);
            return null;
        }

        // User anhand E-Mail finden
        $this->logger->info('[OAuth2] Suche User mit E-Mail', ['email' => $username]);
        
        try {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $username]);
            
            if (!$user) {
                $this->logger->warning('[OAuth2] User nicht gefunden für E-Mail', ['email' => $username]);
                return null;
            }
            
            $this->logger->info('[OAuth2] User gefunden', [
                'id' => $user->getId()->getValue(),
                'email' => $user->getEmail()->getValue(),
                'isActive' => $user->isActive(),
                'hasPassword' => $user->getPassword() !== null
            ]);
            
            if (!$user->isActive()) {
                $this->logger->warning('[OAuth2] User ist nicht aktiv', ['email' => $username]);
                return null;
            }

            // Passwort prüfen mit Symfony PasswordHasher
            if (!$user->getPassword()) {
                $this->logger->warning('[OAuth2] User hat kein Passwort-Hash', ['email' => $username]);
                return null;
            }

            $this->logger->info('[OAuth2] Prüfe Passwort für User', [
                'email' => $username,
                'hasPasswordHash' => strlen($user->getPassword()) > 0,
                'passwordHashLength' => strlen($user->getPassword())
            ]);
            
            $isPasswordValid = $this->passwordHasher->isPasswordValid($user, $password);
            $this->logger->info('[OAuth2] Password validation result', [
                'email' => $username,
                'isValid' => $isPasswordValid
            ]);
            
            if ($isPasswordValid) {
                $this->logger->info('[OAuth2] Passwort ist korrekt für User', ['email' => $username]);
                return $user;
            }

            $this->logger->warning('[OAuth2] Passwort ist falsch für User', ['email' => $username]);
            return null;
            
        } catch (\Exception $e) {
            $this->logger->error('[OAuth2] Exception in getUserEntityByUserCredentials', [
                'email' => $username,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
} 