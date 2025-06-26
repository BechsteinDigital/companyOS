<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Role\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LeagueClientFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // League OAuth2 Client für oauth2_client Tabelle
        $qb = $manager->getConnection()->createQueryBuilder();
        $qb->insert('oauth2_client')
           ->values([
               'identifier' => ':identifier',
               'name' => ':name',
               'secret' => ':secret',
               'redirect_uris' => ':redirect_uris',
               'grants' => ':grants',
               'scopes' => ':scopes',
               'active' => ':active',
               'allow_plain_text_pkce' => ':allow_plain_text_pkce'
           ])
           ->setParameters([
               'identifier' => 'backend',
               'name' => 'Backend',
               'secret' => null, // Kein Secret für Password Grant
               'redirect_uris' => null, // null statt leeres Array für Password Grant
               'grants' => json_encode(['password', 'refresh token']),
               'scopes' => json_encode([
                   'user.read', 'user.write',
                   'role.read', 'role.write',
                   'plugin.read', 'plugin.write',
                   'settings.read', 'settings.write',
                   'webhook.read', 'webhook.write',
                   'client.read', 'client.write',
                   'profile.read', 'profile.write',
                   'auth.read', 'auth.write'
               ]),
               'active' => 1,
               'allow_plain_text_pkce' => 0
           ]);

        $qb->executeQuery();
    }
} 