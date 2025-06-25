<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Infrastructure\Event;

use CompanyOS\Domain\Auth\Domain\Event\UserAuthenticated;
use CompanyOS\Domain\Auth\Domain\Event\UserLoginFailed;
use CompanyOS\Domain\Auth\Domain\Event\UserLoggedOut;
use CompanyOS\Domain\Auth\Infrastructure\External\EmailService;
use CompanyOS\Domain\Auth\Infrastructure\External\NotificationService;
use CompanyOS\Domain\Auth\Infrastructure\External\SecurityAuditService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AuthenticationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SecurityAuditService $securityAuditService,
        private readonly EmailService $emailService,
        private readonly NotificationService $notificationService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserAuthenticated::class => [
                ['onUserAuthenticated', 10],
                ['logAuthentication', 5],
            ],
            UserLoginFailed::class => [
                ['onUserLoginFailed', 10],
                ['logFailedLogin', 5],
            ],
            UserLoggedOut::class => [
                ['onUserLoggedOut', 10],
                ['logLogout', 5],
            ],
        ];
    }

    public function onUserAuthenticated(UserAuthenticated $event): void
    {
        // Security Audit
        $this->securityAuditService->recordSuccessfulLogin(
            $event->getUserId()->value(),
            $event->getEmail()->value(),
            $event->getIpAddress(),
            $event->getUserAgent()
        );

        // Email-Benachrichtigung bei ungewöhnlichen Logins
        if ($this->securityAuditService->isUnusualLogin($event->getIpAddress(), $event->getUserId())) {
            $this->emailService->sendUnusualLoginNotification(
                $event->getEmail()->value(),
                $event->getIpAddress(),
                $event->getUserAgent()
            );
        }

        // Push-Notification für mobile Apps
        $this->notificationService->sendLoginNotification(
            $event->getUserId()->value(),
            'Neuer Login erkannt',
            'Ihr Account wurde erfolgreich angemeldet.'
        );
    }

    public function onUserLoginFailed(UserLoginFailed $event): void
    {
        // Security Audit
        $this->securityAuditService->recordFailedLogin(
            $event->getEmail()->value(),
            $event->getIpAddress(),
            $event->getUserAgent(),
            $event->getReason()
        );

        // Rate Limiting Check
        if ($this->securityAuditService->shouldBlockIp($event->getIpAddress())) {
            $this->securityAuditService->blockIpAddress($event->getIpAddress());
        }

        // Email-Benachrichtigung bei zu vielen Fehlversuchen
        if ($this->securityAuditService->shouldNotifyUser($event->getEmail())) {
            $this->emailService->sendFailedLoginNotification(
                $event->getEmail()->value(),
                $event->getIpAddress()
            );
        }
    }

    public function onUserLoggedOut(UserLoggedOut $event): void
    {
        // Security Audit
        $this->securityAuditService->recordLogout(
            $event->getUserId()->value(),
            $event->getIpAddress(),
            $event->getUserAgent()
        );

        // Session Cleanup
        $this->securityAuditService->cleanupUserSessions($event->getUserId());
    }

    public function logAuthentication(UserAuthenticated $event): void
    {
        $this->logger->info('User authenticated successfully', [
            'userId' => $event->getUserId()->value(),
            'email' => $event->getEmail()->value(),
            'ipAddress' => $event->getIpAddress(),
            'userAgent' => $event->getUserAgent(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function logFailedLogin(UserLoginFailed $event): void
    {
        $this->logger->warning('User login failed', [
            'email' => $event->getEmail()->value(),
            'ipAddress' => $event->getIpAddress(),
            'userAgent' => $event->getUserAgent(),
            'reason' => $event->getReason(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function logLogout(UserLoggedOut $event): void
    {
        $this->logger->info('User logged out', [
            'userId' => $event->getUserId()->value(),
            'ipAddress' => $event->getIpAddress(),
            'userAgent' => $event->getUserAgent(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ]);
    }
} 