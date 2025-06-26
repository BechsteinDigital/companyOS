<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Settings\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Settings\Query\GetCompanySettingsQuery;
use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Entity\CompanySettings;
use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandlerInterface;

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