<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Plugin\Infrastructure\External;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

final class PluginNotificationService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly LoggerInterface $logger,
        private readonly string $fromEmail = 'noreply@companyos.com',
        private readonly string $fromName = 'App Plugin System'
    ) {
    }

    public function sendPluginInstalledNotification(
        string $pluginName,
        string $version,
        string $author
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to('admin@companyos.com')
                ->subject('Plugin installiert: ' . $pluginName)
                ->html($this->twig->render('emails/plugin/installed.html.twig', [
                    'pluginName' => $pluginName,
                    'version' => $version,
                    'author' => $author,
                    'timestamp' => new \DateTimeImmutable()
                ]));

            $this->mailer->send($email);

            $this->logger->info('Plugin installed notification sent', [
                'pluginName' => $pluginName,
                'version' => $version
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send plugin installed notification', [
                'pluginName' => $pluginName,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendPluginActivatedNotification(
        string $pluginName,
        string $version
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to('admin@companyos.com')
                ->subject('Plugin aktiviert: ' . $pluginName)
                ->html($this->twig->render('emails/plugin/activated.html.twig', [
                    'pluginName' => $pluginName,
                    'version' => $version,
                    'timestamp' => new \DateTimeImmutable()
                ]));

            $this->mailer->send($email);

            $this->logger->info('Plugin activated notification sent', [
                'pluginName' => $pluginName,
                'version' => $version
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send plugin activated notification', [
                'pluginName' => $pluginName,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendPluginDeactivatedNotification(
        string $pluginName,
        string $version
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to('admin@companyos.com')
                ->subject('Plugin deaktiviert: ' . $pluginName)
                ->html($this->twig->render('emails/plugin/deactivated.html.twig', [
                    'pluginName' => $pluginName,
                    'version' => $version,
                    'timestamp' => new \DateTimeImmutable()
                ]));

            $this->mailer->send($email);

            $this->logger->info('Plugin deactivated notification sent', [
                'pluginName' => $pluginName,
                'version' => $version
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send plugin deactivated notification', [
                'pluginName' => $pluginName,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendPluginUpdatedNotification(
        string $pluginName,
        string $oldVersion,
        string $newVersion
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to('admin@companyos.com')
                ->subject('Plugin aktualisiert: ' . $pluginName)
                ->html($this->twig->render('emails/plugin/updated.html.twig', [
                    'pluginName' => $pluginName,
                    'oldVersion' => $oldVersion,
                    'newVersion' => $newVersion,
                    'timestamp' => new \DateTimeImmutable()
                ]));

            $this->mailer->send($email);

            $this->logger->info('Plugin updated notification sent', [
                'pluginName' => $pluginName,
                'oldVersion' => $oldVersion,
                'newVersion' => $newVersion
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send plugin updated notification', [
                'pluginName' => $pluginName,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendPluginDeletedNotification(
        string $pluginName,
        string $version
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to('admin@companyos.com')
                ->subject('Plugin gelöscht: ' . $pluginName)
                ->html($this->twig->render('emails/plugin/deleted.html.twig', [
                    'pluginName' => $pluginName,
                    'version' => $version,
                    'timestamp' => new \DateTimeImmutable()
                ]));

            $this->mailer->send($email);

            $this->logger->info('Plugin deleted notification sent', [
                'pluginName' => $pluginName,
                'version' => $version
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send plugin deleted notification', [
                'pluginName' => $pluginName,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendPluginErrorNotification(
        string $pluginName,
        string $error,
        array $context = []
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to('admin@companyos.com')
                ->subject('Plugin Fehler: ' . $pluginName)
                ->html($this->twig->render('emails/plugin/error.html.twig', [
                    'pluginName' => $pluginName,
                    'error' => $error,
                    'context' => $context,
                    'timestamp' => new \DateTimeImmutable()
                ]));

            $this->mailer->send($email);

            $this->logger->error('Plugin error notification sent', [
                'pluginName' => $pluginName,
                'error' => $error,
                'context' => $context
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send plugin error notification', [
                'pluginName' => $pluginName,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendPluginSecurityAlert(
        string $pluginName,
        string $alertType,
        array $details = []
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to('security@companyos.com')
                ->subject('Plugin Security Alert: ' . $pluginName)
                ->html($this->twig->render('emails/plugin/security_alert.html.twig', [
                    'pluginName' => $pluginName,
                    'alertType' => $alertType,
                    'details' => $details,
                    'timestamp' => new \DateTimeImmutable()
                ]));

            $this->mailer->send($email);

            $this->logger->warning('Plugin security alert sent', [
                'pluginName' => $pluginName,
                'alertType' => $alertType,
                'details' => $details
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send plugin security alert', [
                'pluginName' => $pluginName,
                'error' => $e->getMessage()
            ]);
        }
    }
} 