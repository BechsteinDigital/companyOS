<?php

namespace CompanyOS\Plugin;

use CompanyOS\Domain\Plugin\Infrastructure\DependencyInjection\PluginExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PluginBundle extends Bundle
{
    public function getContainerExtension(): PluginExtension
    {
        return new PluginExtension();
    }
} 