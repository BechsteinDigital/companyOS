<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Service;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\ValueObject\UserName;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function isUsernameUnique(UserName $username, ?User $excludeUser = null): bool
    {
        $existingUser = $this->userRepository->findByUsername($username->getValue());
        
        if (!$existingUser) {
            return true;
        }

        if ($excludeUser && $existingUser->getId()->equals($excludeUser->getId())) {
            return true;
        }

        return false;
    }

    public function isEmailUnique(Email $email, ?User $excludeUser = null): bool
    {
        $existingUser = $this->userRepository->findByEmail($email);
        
        if (!$existingUser) {
            return true;
        }

        if ($excludeUser && $existingUser->getId()->equals($excludeUser->getId())) {
            return true;
        }

        return false;
    }

    public function generateUniqueUsername(string $firstName, string $lastName): UserName
    {
        $baseUsername = strtolower($firstName . '.' . $lastName);
        $baseUsername = preg_replace('/[^a-z0-9.]/', '', $baseUsername);
        
        $username = $baseUsername;
        $counter = 1;
        
        while (!$this->isUsernameUnique(new UserName($username))) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return new UserName($username);
    }

    public function validatePasswordStrength(string $password): bool
    {
        // Mindestens 8 Zeichen
        if (strlen($password) < 8) {
            return false;
        }
        
        // Mindestens ein Großbuchstabe
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        // Mindestens ein Kleinbuchstabe
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        // Mindestens eine Zahl
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
} 