<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Role\Fixtures;

use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $client = new Client();
        $client->setClientId('backend');
        $client->setClientName('Backend');
        $client->setRedirectUris([]); // Keine Redirects für Password Grant
        $client->setScopes([
            'user.read', 'user.write',
            'role.read', 'role.write',
            'plugin.read', 'plugin.write',
            'settings.read', 'settings.write',
            'webhook.read', 'webhook.write',
            'client.read', 'client.write',
            'profile.read', 'profile.write',
            'auth.read', 'auth.write'
        ]);
        $client->setIsActive(true);
        $manager->persist($client);
        $manager->flush();
    }
} 