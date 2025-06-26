<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Entity;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'company_settings')]
class CompanySettings
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    // Company Information
    #[ORM\Column(type: 'string', length: 255)]
    private string $companyName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $legalName;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $taxNumber;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $vatNumber;

    // Address
    #[ORM\Column(type: 'string', length: 255)]
    private string $street;

    #[ORM\Column(type: 'string', length: 20)]
    private string $houseNumber;

    #[ORM\Column(type: 'string', length: 20)]
    private string $postalCode;

    #[ORM\Column(type: 'string', length: 255)]
    private string $city;

    #[ORM\Column(type: 'string', length: 100)]
    private string $country;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $state;

    // Contact Information
    #[ORM\Column(type: 'email')]
    private Email $email;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $fax;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $website;

    #[ORM\Column(type: 'email', nullable: true)]
    private ?Email $supportEmail;

    // Localization Settings
    #[ORM\Column(type: 'string', length: 10)]
    private string $defaultLanguage = 'de';

    #[ORM\Column(type: 'string', length: 10)]
    private string $defaultCurrency = 'EUR';

    #[ORM\Column(type: 'string', length: 50)]
    private string $timezone = 'Europe/Berlin';

    #[ORM\Column(type: 'string', length: 20)]
    private string $dateFormat = 'd.m.Y';

    #[ORM\Column(type: 'string', length: 20)]
    private string $timeFormat = 'H:i';

    #[ORM\Column(type: 'string', length: 20)]
    private string $numberFormat = '1.234,56';

    // System Settings
    #[ORM\Column(type: 'string', length: 100)]
    private string $systemName = 'App';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $logoUrl;

    #[ORM\Column(type: 'string', length: 100)]
    private string $defaultUserRole = 'user';

    #[ORM\Column(type: 'integer')]
    private int $sessionTimeout = 3600;

    #[ORM\Column(type: 'boolean')]
    private bool $maintenanceMode = false;

    // Email Settings
    #[ORM\Column(type: 'string', length: 255)]
    private string $emailFromName;

    #[ORM\Column(type: 'email')]
    private Email $emailFromAddress;

    #[ORM\Column(type: 'email', nullable: true)]
    private ?Email $emailReplyTo;

    #[ORM\Column(type: 'string', length: 255)]
    private string $smtpHost;

    #[ORM\Column(type: 'integer')]
    private int $smtpPort = 587;

    #[ORM\Column(type: 'string', length: 10)]
    private string $smtpEncryption = 'tls';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $smtpUsername;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $smtpPassword;

    // Salutations (JSON)
    #[ORM\Column(type: 'json')]
    private array $salutations = [
        'formal' => 'Sehr geehrte/r {lastName}',
        'informal' => 'Hallo {firstName}',
        'gender_male' => 'Sehr geehrter Herr {lastName}',
        'gender_female' => 'Sehr geehrte Frau {lastName}',
        'gender_neutral' => 'Guten Tag {firstName} {lastName}'
    ];

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $companyName,
        string $street,
        string $houseNumber,
        string $postalCode,
        string $city,
        string $country,
        Email $email,
        string $smtpHost,
        Email $emailFromAddress,
        string $emailFromName
    ) {
        $this->id = Uuid::random();
        $this->companyName = $companyName;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
        $this->email = $email;
        $this->smtpHost = $smtpHost;
        $this->emailFromAddress = $emailFromAddress;
        $this->emailFromName = $emailFromName;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getSupportEmail(): ?Email
    {
        return $this->supportEmail;
    }

    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    public function getDefaultCurrency(): string
    {
        return $this->defaultCurrency;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public function getTimeFormat(): string
    {
        return $this->timeFormat;
    }

    public function getNumberFormat(): string
    {
        return $this->numberFormat;
    }

    public function getSystemName(): string
    {
        return $this->systemName;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function getDefaultUserRole(): string
    {
        return $this->defaultUserRole;
    }

    public function getSessionTimeout(): int
    {
        return $this->sessionTimeout;
    }

    public function isMaintenanceMode(): bool
    {
        return $this->maintenanceMode;
    }

    public function getEmailFromName(): string
    {
        return $this->emailFromName;
    }

    public function getEmailFromAddress(): Email
    {
        return $this->emailFromAddress;
    }

    public function getEmailReplyTo(): ?Email
    {
        return $this->emailReplyTo;
    }

    public function getSmtpHost(): string
    {
        return $this->smtpHost;
    }

    public function getSmtpPort(): int
    {
        return $this->smtpPort;
    }

    public function getSmtpEncryption(): string
    {
        return $this->smtpEncryption;
    }

    public function getSmtpUsername(): ?string
    {
        return $this->smtpUsername;
    }

    public function getSmtpPassword(): ?string
    {
        return $this->smtpPassword;
    }

    public function getSalutations(): array
    {
        return $this->salutations;
    }

    public function getSalutation(string $type): ?string
    {
        return $this->salutations[$type] ?? null;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // Setters
    public function updateCompanyInfo(
        string $companyName,
        ?string $legalName = null,
        ?string $taxNumber = null,
        ?string $vatNumber = null
    ): void {
        $this->companyName = $companyName;
        $this->legalName = $legalName;
        $this->taxNumber = $taxNumber;
        $this->vatNumber = $vatNumber;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateAddress(
        string $street,
        string $houseNumber,
        string $postalCode,
        string $city,
        string $country,
        ?string $state = null
    ): void {
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
        $this->state = $state;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateContactInfo(
        Email $email,
        ?string $phone = null,
        ?string $fax = null,
        ?string $website = null,
        ?Email $supportEmail = null
    ): void {
        $this->email = $email;
        $this->phone = $phone;
        $this->fax = $fax;
        $this->website = $website;
        $this->supportEmail = $supportEmail;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateLocalization(
        string $defaultLanguage,
        string $defaultCurrency,
        string $timezone,
        string $dateFormat,
        string $timeFormat,
        string $numberFormat
    ): void {
        $this->defaultLanguage = $defaultLanguage;
        $this->defaultCurrency = $defaultCurrency;
        $this->timezone = $timezone;
        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
        $this->numberFormat = $numberFormat;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateSystemSettings(
        string $systemName,
        ?string $logoUrl = null,
        string $defaultUserRole = 'user',
        int $sessionTimeout = 3600,
        bool $maintenanceMode = false
    ): void {
        $this->systemName = $systemName;
        $this->logoUrl = $logoUrl;
        $this->defaultUserRole = $defaultUserRole;
        $this->sessionTimeout = $sessionTimeout;
        $this->maintenanceMode = $maintenanceMode;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateEmailSettings(
        string $emailFromName,
        Email $emailFromAddress,
        ?Email $emailReplyTo = null,
        string $smtpHost = '',
        int $smtpPort = 587,
        string $smtpEncryption = 'tls',
        ?string $smtpUsername = null,
        ?string $smtpPassword = null
    ): void {
        $this->emailFromName = $emailFromName;
        $this->emailFromAddress = $emailFromAddress;
        $this->emailReplyTo = $emailReplyTo;
        $this->smtpHost = $smtpHost;
        $this->smtpPort = $smtpPort;
        $this->smtpEncryption = $smtpEncryption;
        $this->smtpUsername = $smtpUsername;
        $this->smtpPassword = $smtpPassword;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateSalutations(array $salutations): void
    {
        $this->salutations = $salutations;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function addSalutation(string $type, string $template): void
    {
        $this->salutations[$type] = $template;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function removeSalutation(string $type): void
    {
        unset($this->salutations[$type]);
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Utility Methods
    public function getFullAddress(): string
    {
        $address = $this->street . ' ' . $this->houseNumber . ', ' . $this->postalCode . ' ' . $this->city;
        if ($this->state) {
            $address .= ', ' . $this->state;
        }
        $address .= ', ' . $this->country;
        return $address;
    }

    public function formatSalutation(string $type, array $variables = []): string
    {
        $template = $this->getSalutation($type);
        if (!$template) {
            return '';
        }

        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }
} 