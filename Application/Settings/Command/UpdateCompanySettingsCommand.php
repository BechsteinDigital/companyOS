<?php

namespace CompanyOS\Domain\Settings\Application\Command;

use CompanyOS\Application\Command\CommandInterface;
use CompanyOS\Domain\Shared\ValueObject\Email;

class UpdateCompanySettingsCommand implements CommandInterface
{
    public function __construct(
        public readonly ?string $companyName = null,
        public readonly ?string $legalName = null,
        public readonly ?string $taxNumber = null,
        public readonly ?string $vatNumber = null,
        public readonly ?string $street = null,
        public readonly ?string $houseNumber = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $city = null,
        public readonly ?string $country = null,
        public readonly ?string $state = null,
        public readonly ?Email $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $fax = null,
        public readonly ?string $website = null,
        public readonly ?Email $supportEmail = null,
        public readonly ?string $defaultLanguage = null,
        public readonly ?string $defaultCurrency = null,
        public readonly ?string $timezone = null,
        public readonly ?string $dateFormat = null,
        public readonly ?string $timeFormat = null,
        public readonly ?string $numberFormat = null,
        public readonly ?string $systemName = null,
        public readonly ?string $logoUrl = null,
        public readonly ?string $defaultUserRole = null,
        public readonly ?int $sessionTimeout = null,
        public readonly ?bool $maintenanceMode = null,
        public readonly ?string $emailFromName = null,
        public readonly ?Email $emailFromAddress = null,
        public readonly ?Email $emailReplyTo = null,
        public readonly ?string $smtpHost = null,
        public readonly ?int $smtpPort = null,
        public readonly ?string $smtpEncryption = null,
        public readonly ?string $smtpUsername = null,
        public readonly ?string $smtpPassword = null,
        public readonly ?array $salutations = null
    ) {
    }
} 