<?php

namespace CompanyOS\Domain\Settings\Application\Command;

use CompanyOS\Application\Command\CommandInterface;
use CompanyOS\Domain\ValueObject\Email;

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