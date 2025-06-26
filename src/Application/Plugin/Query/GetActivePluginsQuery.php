<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

final class GetActivePluginsQuery implements Query
{
    public function __construct(
        private readonly ?string $category = null
    ) {
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }
} 