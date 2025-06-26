<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\External;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Psr\Log\LoggerInterface;

final class NotificationService
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function sendLoginNotification(
        string $userId,
        string $title,
        string $message
    ): void {
        try {
            // Hier würde die Integration mit einem Push-Notification-Service erfolgen
            // z.B. Firebase Cloud Messaging, Apple Push Notification Service, etc.
            
            $this->logger->info('Login notification sent', [
                'userId' => $userId,
                'title' => $title,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send login notification', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendSecurityAlert(
        string $userId,
        string $title,
        string $message,
        string $severity = 'warning'
    ): void {
        try {
            // Security-Alert an mobile Apps senden
            $this->logger->info('Security alert sent', [
                'userId' => $userId,
                'title' => $title,
                'message' => $message,
                'severity' => $severity
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send security alert', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendPasswordChangedAlert(
        string $userId,
        string $ipAddress
    ): void {
        $this->sendSecurityAlert(
            $userId,
            'Passwort geändert',
            "Ihr Passwort wurde von IP-Adresse {$ipAddress} geändert.",
            'info'
        );
    }

    public function sendUnusualLoginAlert(
        string $userId,
        string $ipAddress,
        string $location = 'Unbekannter Standort'
    ): void {
        $this->sendSecurityAlert(
            $userId,
            'Ungewöhnlicher Login',
            "Neuer Login von {$location} (IP: {$ipAddress}) erkannt.",
            'warning'
        );
    }

    public function sendAccountLockedAlert(
        string $userId,
        string $reason
    ): void {
        $this->sendSecurityAlert(
            $userId,
            'Account gesperrt',
            "Ihr Account wurde temporär gesperrt: {$reason}",
            'error'
        );
    }

    public function sendFailedLoginAttemptAlert(
        string $userId,
        int $attemptCount
    ): void {
        $this->sendSecurityAlert(
            $userId,
            'Fehlgeschlagene Login-Versuche',
            "Es wurden {$attemptCount} fehlgeschlagene Login-Versuche erkannt.",
            'warning'
        );
    }

    public function sendSessionExpiredNotification(
        string $userId
    ): void {
        $this->sendSecurityAlert(
            $userId,
            'Session abgelaufen',
            'Ihre Session ist abgelaufen. Bitte melden Sie sich erneut an.',
            'info'
        );
    }

    public function sendNewDeviceLoginNotification(
        string $userId,
        string $deviceInfo
    ): void {
        $this->sendSecurityAlert(
            $userId,
            'Neues Gerät angemeldet',
            "Ein neues Gerät wurde angemeldet: {$deviceInfo}",
            'info'
        );
    }
} 