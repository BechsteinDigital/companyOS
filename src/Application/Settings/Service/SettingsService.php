<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Settings\Service;

use CompanyOS\Bundle\CoreBundle\Application\Settings\Query\GetCompanySettingsQuery;
use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Entity\CompanySettings;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class SettingsService
{
    private ?CompanySettings $cachedSettings = null;
    private bool $cacheInitialized = false;

    public function __construct(
        private MessageBusInterface $queryBus
    ) {
    }

    public function getSettings(): ?CompanySettings
    {
        if (!$this->cacheInitialized) {
            $envelope = $this->queryBus->dispatch(new GetCompanySettingsQuery());
            $this->cachedSettings = $envelope->last(HandledStamp::class)?->getResult();
            $this->cacheInitialized = true;
        }
        return $this->cachedSettings;
    }

    public function refreshCache(): void
    {
        $envelope = $this->queryBus->dispatch(new GetCompanySettingsQuery());
        $this->cachedSettings = $envelope->last(HandledStamp::class)?->getResult();
        $this->cacheInitialized = true;
    }

    public function clearCache(): void
    {
        $this->cachedSettings = null;
        $this->cacheInitialized = false;
    }

    // Convenience methods for common settings
    public function getCompanyName(): ?string
    {
        return $this->getSettings()?->getCompanyName();
    }

    public function getCompanyEmail(): ?string
    {
        return $this->getSettings()?->getEmail()?->toString();
    }

    public function getSystemName(): ?string
    {
        return $this->getSettings()?->getSystemName();
    }

    public function getDefaultLanguage(): ?string
    {
        return $this->getSettings()?->getDefaultLanguage();
    }

    public function getDefaultCurrency(): ?string
    {
        return $this->getSettings()?->getDefaultCurrency();
    }

    public function getTimezone(): ?string
    {
        return $this->getSettings()?->getTimezone();
    }

    public function getDateFormat(): ?string
    {
        return $this->getSettings()?->getDateFormat();
    }

    public function getTimeFormat(): ?string
    {
        return $this->getSettings()?->getTimeFormat();
    }

    public function getNumberFormat(): ?string
    {
        return $this->getSettings()?->getNumberFormat();
    }

    public function getLogoUrl(): ?string
    {
        return $this->getSettings()?->getLogoUrl();
    }

    public function isMaintenanceMode(): bool
    {
        return $this->getSettings()?->isMaintenanceMode() ?? false;
    }

    public function getSessionTimeout(): int
    {
        return $this->getSettings()?->getSessionTimeout() ?? 3600;
    }

    public function getDefaultUserRole(): ?string
    {
        return $this->getSettings()?->getDefaultUserRole();
    }

    public function getSalutations(): array
    {
        return $this->getSettings()?->getSalutations() ?? [];
    }

    public function getSalutation(string $type): ?string
    {
        return $this->getSettings()?->getSalutation($type);
    }

    public function formatSalutation(string $type, array $variables = []): string
    {
        $settings = $this->getSettings();
        if (!$settings) {
            return '';
        }
        return $settings->formatSalutation($type, $variables);
    }

    public function getFullAddress(): ?string
    {
        return $this->getSettings()?->getFullAddress();
    }

    // Email settings
    public function getEmailFromName(): ?string
    {
        return $this->getSettings()?->getEmailFromName();
    }

    public function getEmailFromAddress(): ?string
    {
        return $this->getSettings()?->getEmailFromAddress()?->toString();
    }

    public function getEmailReplyTo(): ?string
    {
        return $this->getSettings()?->getEmailReplyTo()?->toString();
    }

    public function getSmtpHost(): ?string
    {
        return $this->getSettings()?->getSmtpHost();
    }

    public function getSmtpPort(): ?int
    {
        return $this->getSettings()?->getSmtpPort();
    }

    public function getSmtpEncryption(): ?string
    {
        return $this->getSettings()?->getSmtpEncryption();
    }

    public function getSmtpUsername(): ?string
    {
        return $this->getSettings()?->getSmtpUsername();
    }

    public function getSmtpPassword(): ?string
    {
        return $this->getSettings()?->getSmtpPassword();
    }

    // Utility methods for formatting
    public function formatDate(\DateTimeInterface $date): string
    {
        $format = $this->getDateFormat() ?? 'd.m.Y';
        return $date->format($format);
    }

    public function formatTime(\DateTimeInterface $time): string
    {
        $format = $this->getTimeFormat() ?? 'H:i';
        return $time->format($format);
    }

    public function formatDateTime(\DateTimeInterface $datetime): string
    {
        $dateFormat = $this->getDateFormat() ?? 'd.m.Y';
        $timeFormat = $this->getTimeFormat() ?? 'H:i';
        return $datetime->format($dateFormat . ' ' . $timeFormat);
    }

    public function formatNumber(float $number): string
    {
        $format = $this->getNumberFormat() ?? '1.234,56';
        if (str_contains($format, ',')) {
            return number_format($number, 2, ',', '.');
        } else {
            return number_format($number, 2, '.', ',');
        }
    }

    public function formatCurrency(float $amount): string
    {
        $currency = $this->getDefaultCurrency() ?? 'EUR';
        $formattedNumber = $this->formatNumber($amount);
        return $formattedNumber . ' ' . $currency;
    }
} 