<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Plugin\Application\QueryHandler;

use CompanyOS\Domain\Plugin\Application\Query\CheckPluginCompatibilityQuery;
use CompanyOS\Domain\Plugin\Application\DTO\PluginCompatibilityResponse;
use CompanyOS\Domain\Plugin\Domain\Service\PluginCompatibilityService;
use CompanyOS\Application\Query\QueryHandlerInterface;

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