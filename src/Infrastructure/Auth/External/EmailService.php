<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\External;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

final class EmailService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly LoggerInterface $logger,
        private readonly string $fromEmail = 'noreply@companyos.com',
        private readonly string $fromName = 'App Security'
    ) {
    }

    public function sendUnusualLoginNotification(
        string $userEmail,
        string $ipAddress,
        string $userAgent
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($userEmail)
                ->subject('Ungewöhnlicher Login erkannt')
                ->html($this->twig->render('emails/auth/unusual_login.html.twig', [
                    'ipAddress' => $ipAddress,
                    'userAgent' => $userAgent,
                    'timestamp' => new \DateTimeImmutable(),
                    'supportEmail' => 'support@companyos.com'
                ]));

            $this->mailer->send($email);

            $this->logger->info('Unusual login notification sent', [
                'email' => $userEmail,
                'ipAddress' => $ipAddress
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send unusual login notification', [
                'email' => $userEmail,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendFailedLoginNotification(
        string $userEmail,
        string $ipAddress
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($userEmail)
                ->subject('Mehrere fehlgeschlagene Login-Versuche')
                ->html($this->twig->render('emails/auth/failed_login_attempts.html.twig', [
                    'ipAddress' => $ipAddress,
                    'timestamp' => new \DateTimeImmutable(),
                    'supportEmail' => 'support@companyos.com'
                ]));

            $this->mailer->send($email);

            $this->logger->info('Failed login notification sent', [
                'email' => $userEmail,
                'ipAddress' => $ipAddress
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send failed login notification', [
                'email' => $userEmail,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendPasswordResetEmail(
        string $userEmail,
        string $resetToken,
        \DateTimeImmutable $expiresAt
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($userEmail)
                ->subject('Passwort zurücksetzen')
                ->html($this->twig->render('emails/auth/password_reset.html.twig', [
                    'resetToken' => $resetToken,
                    'expiresAt' => $expiresAt,
                    'supportEmail' => 'support@companyos.com'
                ]));

            $this->mailer->send($email);

            $this->logger->info('Password reset email sent', [
                'email' => $userEmail
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send password reset email', [
                'email' => $userEmail,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendPasswordChangedNotification(
        string $userEmail,
        string $ipAddress
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($userEmail)
                ->subject('Passwort geändert')
                ->html($this->twig->render('emails/auth/password_changed.html.twig', [
                    'ipAddress' => $ipAddress,
                    'timestamp' => new \DateTimeImmutable(),
                    'supportEmail' => 'support@companyos.com'
                ]));

            $this->mailer->send($email);

            $this->logger->info('Password changed notification sent', [
                'email' => $userEmail,
                'ipAddress' => $ipAddress
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send password changed notification', [
                'email' => $userEmail,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendAccountLockedNotification(
        string $userEmail,
        string $reason
    ): void {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($userEmail)
                ->subject('Account temporär gesperrt')
                ->html($this->twig->render('emails/auth/account_locked.html.twig', [
                    'reason' => $reason,
                    'timestamp' => new \DateTimeImmutable(),
                    'supportEmail' => 'support@companyos.com'
                ]));

            $this->mailer->send($email);

            $this->logger->info('Account locked notification sent', [
                'email' => $userEmail,
                'reason' => $reason
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to send account locked notification', [
                'email' => $userEmail,
                'error' => $e->getMessage()
            ]);
        }
    }
} 