<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Role\Fixtures;

use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\Role;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleName;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleDisplayName;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleDescription;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RolePermissions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Admin-Rolle mit allen Permissions
        $adminRole = new Role(
            new RoleName('admin'),
            new RoleDisplayName('Administrator'),
            new RoleDescription('Vollzugriff auf alle Funktionen'),
            new RolePermissions([
                'user.read', 'user.write',
                'role.read', 'role.write',
                'plugin.read', 'plugin.write',
                'settings.read', 'settings.write',
                'webhook.read', 'webhook.write',
                'client.read', 'client.write',
                'profile.read', 'profile.write',
                'auth.read', 'auth.write'
            ]),
            true // isSystem
        );
        $manager->persist($adminRole);

        // Manager-Rolle (Beispiel)
        $managerRole = new Role(
            new RoleName('manager'),
            new RoleDisplayName('Manager'),
            new RoleDescription('Verwaltung von Benutzern und Inhalten'),
            new RolePermissions([
                'user.read', 'user.write',
                'plugin.read', 'plugin.write'
            ]),
            false
        );
        $manager->persist($managerRole);

        // User-Rolle (Beispiel)
        $userRole = new Role(
            new RoleName('user'),
            new RoleDisplayName('Benutzer'),
            new RoleDescription('Standard-Benutzer mit Basisrechten'),
            new RolePermissions([
                'profile.read', 'profile.write'
            ]),
            false
        );
        $manager->persist($userRole);

        $manager->flush();
    }
} 