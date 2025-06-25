<?php

namespace CompanyOS\Application\Settings\DTO;

use CompanyOS\Domain\Settings\Domain\Entity\CompanySettings;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CompanySettingsResponse',
    title: 'Company Settings Response',
    description: 'Response containing all company settings'
)]
class CompanySettingsResponse
{
    public function __construct(
        #[OA\Property(description: 'Unique identifier', example: '550e8400-e29b-41d4-a716-446655440000')]
        public readonly string $id,

        #[OA\Property(description: 'Company name', example: 'Musterfirma GmbH')]
        public readonly string $companyName,

        #[OA\Property(description: 'Legal company name', example: 'Musterfirma Gesellschaft mit beschränkter Haftung')]
        public readonly ?string $legalName,

        #[OA\Property(description: 'Tax number', example: '123/456/78901')]
        public readonly ?string $taxNumber,

        #[OA\Property(description: 'VAT number', example: 'DE123456789')]
        public readonly ?string $vatNumber,

        #[OA\Property(description: 'Street address', example: 'Musterstraße')]
        public readonly string $street,

        #[OA\Property(description: 'House number', example: '123')]
        public readonly string $houseNumber,

        #[OA\Property(description: 'Postal code', example: '12345')]
        public readonly string $postalCode,

        #[OA\Property(description: 'City', example: 'Musterstadt')]
        public readonly string $city,

        #[OA\Property(description: 'Country', example: 'Deutschland')]
        public readonly string $country,

        #[OA\Property(description: 'State/Province', example: 'Bayern')]
        public readonly ?string $state,

        #[OA\Property(description: 'Company email', example: 'info@musterfirma.de')]
        public readonly string $email,

        #[OA\Property(description: 'Phone number', example: '+49 123 456789')]
        public readonly ?string $phone,

        #[OA\Property(description: 'Fax number', example: '+49 123 456788')]
        public readonly ?string $fax,

        #[OA\Property(description: 'Website URL', example: 'https://www.musterfirma.de')]
        public readonly ?string $website,

        #[OA\Property(description: 'Support email', example: 'support@musterfirma.de')]
        public readonly ?string $supportEmail,

        #[OA\Property(description: 'Default language', example: 'de')]
        public readonly string $defaultLanguage,

        #[OA\Property(description: 'Default currency', example: 'EUR')]
        public readonly string $defaultCurrency,

        #[OA\Property(description: 'Timezone', example: 'Europe/Berlin')]
        public readonly string $timezone,

        #[OA\Property(description: 'Date format', example: 'd.m.Y')]
        public readonly string $dateFormat,

        #[OA\Property(description: 'Time format', example: 'H:i')]
        public readonly string $timeFormat,

        #[OA\Property(description: 'Number format', example: '1.234,56')]
        public readonly string $numberFormat,

        #[OA\Property(description: 'System name', example: 'App')]
        public readonly string $systemName,

        #[OA\Property(description: 'Logo URL', example: 'https://example.com/logo.png')]
        public readonly ?string $logoUrl,

        #[OA\Property(description: 'Default user role', example: 'user')]
        public readonly string $defaultUserRole,

        #[OA\Property(description: 'Session timeout in seconds', example: 3600)]
        public readonly int $sessionTimeout,

        #[OA\Property(description: 'Maintenance mode enabled', example: false)]
        public readonly bool $maintenanceMode,

        #[OA\Property(description: 'Email from name', example: 'Musterfirma GmbH')]
        public readonly string $emailFromName,

        #[OA\Property(description: 'Email from address', example: 'noreply@musterfirma.de')]
        public readonly string $emailFromAddress,

        #[OA\Property(description: 'Email reply-to address', example: 'reply@musterfirma.de')]
        public readonly ?string $emailReplyTo,

        #[OA\Property(description: 'SMTP host', example: 'smtp.musterfirma.de')]
        public readonly string $smtpHost,

        #[OA\Property(description: 'SMTP port', example: 587)]
        public readonly int $smtpPort,

        #[OA\Property(description: 'SMTP encryption', example: 'tls')]
        public readonly string $smtpEncryption,

        #[OA\Property(description: 'SMTP username', example: 'smtp@musterfirma.de')]
        public readonly ?string $smtpUsername,

        #[OA\Property(description: 'Available salutations', type: 'object')]
        public readonly array $salutations,

        #[OA\Property(description: 'Full formatted address', example: 'Musterstraße 123, 12345 Musterstadt, Deutschland')]
        public readonly string $fullAddress,

        #[OA\Property(description: 'Creation timestamp')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(description: 'Last update timestamp')]
        public readonly \DateTimeImmutable $updatedAt
    ) {
    }

    public static function fromEntity(CompanySettings $settings): self
    {
        return new self(
            $settings->getId()->toString(),
            $settings->getCompanyName(),
            $settings->getLegalName(),
            $settings->getTaxNumber(),
            $settings->getVatNumber(),
            $settings->getStreet(),
            $settings->getHouseNumber(),
            $settings->getPostalCode(),
            $settings->getCity(),
            $settings->getCountry(),
            $settings->getState(),
            $settings->getEmail()->toString(),
            $settings->getPhone(),
            $settings->getFax(),
            $settings->getWebsite(),
            $settings->getSupportEmail()?->toString(),
            $settings->getDefaultLanguage(),
            $settings->getDefaultCurrency(),
            $settings->getTimezone(),
            $settings->getDateFormat(),
            $settings->getTimeFormat(),
            $settings->getNumberFormat(),
            $settings->getSystemName(),
            $settings->getLogoUrl(),
            $settings->getDefaultUserRole(),
            $settings->getSessionTimeout(),
            $settings->isMaintenanceMode(),
            $settings->getEmailFromName(),
            $settings->getEmailFromAddress()->toString(),
            $settings->getEmailReplyTo()?->toString(),
            $settings->getSmtpHost(),
            $settings->getSmtpPort(),
            $settings->getSmtpEncryption(),
            $settings->getSmtpUsername(),
            $settings->getSalutations(),
            $settings->getFullAddress(),
            $settings->getCreatedAt(),
            $settings->getUpdatedAt()
        );
    }
} 