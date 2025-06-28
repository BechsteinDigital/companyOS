<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\EventListener;

use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence\DoctrineUserRepository;
use League\Bundle\OAuth2ServerBundle\Event\UserResolveEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserResolveEventListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly DoctrineUserRepository $userRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OAuth2Events::USER_RESOLVE => ['onUserResolve', 100],
        ];
    }

    public function onUserResolve(UserResolveEvent $event): void
    {
        $this->logger->info('[OAuth2 UserResolve] Event received', [
            'username' => $event->getUsername(),
            'grantType' => $event->getGrant()->getGrantType(),
            'clientId' => $event->getClient()->getIdentifier(),
            'passwordLength' => strlen($event->getPassword()),
        ]);

        try {
            // Verwende unsere DoctrineUserRepository
            $user = $this->userRepository->getUserEntityByUserCredentials(
                $event->getUsername(),
                $event->getPassword(),
                $event->getGrant()->getGrantType(),
                $event->getClient()
            );

            if ($user) {
                $this->logger->info('[OAuth2 UserResolve] User found and authenticated', [
                    'username' => $event->getUsername(),
                    'userId' => $user->getIdentifier(),
                ]);
                
                // Setze den User im Event
                $event->setUser($user);
            } else {
                $this->logger->warning('[OAuth2 UserResolve] User not found or invalid credentials', [
                    'username' => $event->getUsername(),
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->error('[OAuth2 UserResolve] Exception during user resolution', [
                'username' => $event->getUsername(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
} 