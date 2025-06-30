<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class CompanyOSCoreBundle extends Bundle
{
    public function loadRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getPath() . '/Resources/config/routes.yaml');
    }
} 