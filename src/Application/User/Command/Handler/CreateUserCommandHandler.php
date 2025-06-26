<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Command\Handler;

use CompanyOS\Bundle\CoreBundle\Application\User\Command\CreateUserCommand;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepository;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandler;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
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