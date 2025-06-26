<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Command;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleName;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Service\RoleService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'companyos:user:create-admin',
    description: 'Lege einen neuen Admin-Benutzer an'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly RoleService $roleService,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'E-Mail des Admins')
            ->addArgument('password', InputArgument::REQUIRED, 'Passwort des Admins')
            ->addArgument('firstName', InputArgument::REQUIRED, 'Vorname')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Nachname');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = new Email($input->getArgument('email'));
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $plainPassword = $input->getArgument('password');

        // Prüfen, ob User existiert
        if ($this->userRepository->findByEmail($email)) {
            $output->writeln('<error>Ein Benutzer mit dieser E-Mail existiert bereits.</error>');
            return Command::FAILURE;
        }

        $user = new User(
            Uuid::random(),
            $email,
            $firstName,
            $lastName
        );

        $hashed = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPasswordHash($hashed);

        // Admin-Rolle laden
        $adminRole = $this->roleRepository->findByName(new RoleName('admin'));
        if (!$adminRole) {
            $output->writeln('<error>Admin-Rolle (admin) existiert nicht!</error>');
            return Command::FAILURE;
        }

        // Admin-Rolle zuweisen (inkl. aller zugehörigen Permissions)
        $this->roleService->assignRoleToUser($adminRole, $user);

        $this->userRepository->save($user);

        $output->writeln('<info>Admin-Benutzer erfolgreich angelegt und Rolle zugewiesen!</info>');
        return Command::SUCCESS;
    }
}
