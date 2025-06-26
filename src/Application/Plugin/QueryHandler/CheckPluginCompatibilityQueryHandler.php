<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\CheckPluginCompatibilityQuery;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\DTO\PluginCompatibilityResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Service\PluginCompatibilityService;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandlerInterface;

final class CheckPluginCompatibilityQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly PluginCompatibilityService $compatibilityService
    ) {
    }

    public function __invoke(CheckPluginCompatibilityQuery $query): PluginCompatibilityResponse
    {
        $compatibility = $this->compatibilityService->checkCompatibility(
            $query->getPluginName(),
            $query->getVersion(),
            $query->getSystemRequirements()
        );

        return new PluginCompatibilityResponse(
            isCompatible: $compatibility['isCompatible'],
            issues: $compatibility['issues'],
            warnings: $compatibility['warnings'],
            recommendations: $compatibility['recommendations'],
            systemInfo: $compatibility['systemInfo']
        );
    }
} 