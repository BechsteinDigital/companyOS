<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Settings\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email;

class InitializeCompanySettingsCommand implements CommandInterface
{
    public function __construct(
        public readonly string $companyName,
        public readonly string $street,
        public readonly string $houseNumber,
        public readonly string $postalCode,
        public readonly string $city,
        public readonly string $country,
        public readonly Email $email,
        public readonly string $smtpHost,
        public readonly Email $emailFromAddress,
        public readonly string $emailFromName
    ) {
    }
} 