<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\DependencyInjection\Compiler;

use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Converter\UserConverter;
use League\Bundle\OAuth2ServerBundle\Converter\UserConverterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OAuth2UserConverterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Registriere unseren benutzerdefinierten UserConverter
        if ($container->hasDefinition(UserConverter::class)) {
            $container->setAlias(UserConverterInterface::class, UserConverter::class);
        }
    }
} 