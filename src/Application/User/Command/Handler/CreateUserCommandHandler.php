<?php

namespace CompanyOS\Domain\User\Application\Command\Handler;

use CompanyOS\Domain\User\Application\Command\CreateUserCommand;
use CompanyOS\Domain\User\Domain\Entity\User;
use CompanyOS\Domain\User\Domain\Repository\UserRepository;
use CompanyOS\Application\Command\CommandHandler;
use CompanyOS\Domain\ValueObject\Email;
use CompanyOS\Domain\ValueObject\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $email = Email::fromString($command->email);
        
        if ($this->userRepository->existsByEmail($email)) {
            throw new \InvalidArgumentException('User with this email already exists');
        }

        $user = new User(
            Uuid::random(),
            $email,
            $command->firstName,
            $command->lastName
        );

        if ($command->password !== null) {
            $passwordHash = $this->passwordHasher->hashPassword($user, $command->password);
            $user->setPasswordHash($passwordHash);
        }

        $this->userRepository->save($user);
    }
} 