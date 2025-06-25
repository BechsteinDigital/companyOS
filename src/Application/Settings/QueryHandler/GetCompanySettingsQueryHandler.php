<?php

namespace CompanyOS\Domain\Settings\Application\QueryHandler;

use CompanyOS\Domain\Settings\Application\Query\GetCompanySettingsQuery;
use CompanyOS\Domain\Settings\Domain\Entity\CompanySettings;
use CompanyOS\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Application\Query\QueryHandlerInterface;

class GetCompanySettingsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private CompanySettingsRepositoryInterface $repository
    ) {
    }

    public function __invoke(GetCompanySettingsQuery $query): ?CompanySettings
    {
        return $this->repository->find();
    }
} 