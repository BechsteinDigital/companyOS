<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Role\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WebpackClientFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Webpack OAuth2 Client für Build-Prozess
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
               'identifier' => 'webpack-build',
               'name' => 'Webpack Build Client',
               'secret' => password_hash('webpack-secret-' . uniqid(), PASSWORD_BCRYPT),
               'redirect_uris' => null, // Keine Redirects für Client Credentials
               'grants' => json_encode(['client_credentials']),
               'scopes' => json_encode([
                   'plugin.read' // Nur Lesen von Plugin-Informationen
               ]),
               'active' => 1,
               'allow_plain_text_pkce' => 0
           ]);

        $qb->executeQuery();
        
        // Client-Secret in separate Datei schreiben für Webpack
        $secret = password_hash('webpack-secret-' . uniqid(), PASSWORD_BCRYPT);
        $webpackConfig = [
            'client_id' => 'webpack-build',
            'client_secret' => $secret,
            'token_url' => 'http://localhost:8000/oauth/token',
            'api_url' => 'http://localhost:8000/api/plugins'
        ];
        
        $configPath = dirname(__DIR__, 4) . '/webpack-oauth.json';
        file_put_contents($configPath, json_encode($webpackConfig, JSON_PRETTY_PRINT));
        
        echo "Webpack OAuth2 Client erstellt. Konfiguration in: $configPath\n";
    }
} 