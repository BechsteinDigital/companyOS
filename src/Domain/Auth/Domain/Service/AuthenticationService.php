<?php

namespace CompanyOS\Domain\Auth\Domain\Service;

use CompanyOS\Domain\User\Domain\Entity\User;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Domain\User\Domain\ValueObject\UserStatus;
use CompanyOS\Domain\ValueObject\Email;

class AuthenticationService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function authenticateUser(string $username, string $password): ?User
    {
        $user = $this->userRepository->findByUsername($username);
        
        if (!$user) {
            return null;
        }

        if (!$user->getStatus()->canLogin()) {
            return null;
        }

        if (!$user->verifyPassword($password)) {
            return null;
        }

        return $user;
    }

    public function authenticateUserByEmail(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail(new Email($email));
        
        if (!$user) {
            return null;
        }

        if (!$user->getStatus()->canLogin()) {
            return null;
        }

        if (!$user->verifyPassword($password)) {
            return null;
        }

        return $user;
    }

    public function isAccountLocked(User $user): bool
    {
        return $user->getStatus() === UserStatus::SUSPENDED;
    }

    public function canUserLogin(User $user): bool
    {
        return $user->getStatus()->canLogin();
    }

    public function validateLoginAttempts(string $username, string $ipAddress): bool
    {
        // Hier könnte eine Rate-Limiting-Logik implementiert werden
        // Für jetzt geben wir true zurück
        return true;
    }

    public function recordFailedLoginAttempt(string $username, string $ipAddress): void
    {
        // Hier könnte die Logik für das Tracking fehlgeschlagener Login-Versuche implementiert werden
    }

    public function recordSuccessfulLogin(User $user, string $ipAddress): void
    {
        // Hier könnte die Logik für das Tracking erfolgreicher Logins implementiert werden
        $user->updateLastLoginAt(new \DateTimeImmutable());
        $this->userRepository->save($user);
    }
} 