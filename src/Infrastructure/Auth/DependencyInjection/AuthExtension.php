<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\DependencyInjection;

use CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler\ChangePasswordCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler\LoginUserCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler\LogoutUserCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler\RefreshTokenCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler\RequestPasswordResetCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\CommandHandler\ResetPasswordCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\EventHandler\AuthenticationEventHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\QueryHandler\GetActiveSessionsQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\QueryHandler\GetOAuthClientsQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\QueryHandler\GetUserProfileQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\QueryHandler\ValidateTokenQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Auth\Service\AuthenticationApplicationService;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Repository\AccessTokenRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Repository\ClientRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Event\AuthenticationEventDispatcher;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Event\AuthenticationEventSubscriber;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\External\EmailService;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\External\GeoLocationService;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\External\NotificationService;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\External\SecurityAuditService;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence\DoctrineAccessTokenRepository;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence\DoctrineClientRepository;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

final class AuthExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // Repository Bindings
        $container->setAlias(AccessTokenRepositoryInterface::class, DoctrineAccessTokenRepository::class);
        $container->setAlias(ClientRepositoryInterface::class, DoctrineClientRepository::class);

        // Domain Event Dispatcher
        $container->setAlias(DomainEventDispatcher::class, AuthenticationEventDispatcher::class);

        // Command Handlers
        $container->autowire(LoginUserCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        $container->autowire(LogoutUserCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        $container->autowire(RefreshTokenCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        $container->autowire(ChangePasswordCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        $container->autowire(RequestPasswordResetCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        $container->autowire(ResetPasswordCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        // Query Handlers
        $container->autowire(GetUserProfileQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);

        $container->autowire(GetActiveSessionsQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);

        $container->autowire(ValidateTokenQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);

        $container->autowire(GetOAuthClientsQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);

        // Event Handlers
        $container->autowire(AuthenticationEventHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'event.bus']);

        // Event Subscribers
        $container->autowire(AuthenticationEventSubscriber::class)
            ->addTag('kernel.event_subscriber');

        // External Services
        $container->autowire(SecurityAuditService::class)
            ->setPublic(true);

        $container->autowire(EmailService::class)
            ->setPublic(true);

        $container->autowire(NotificationService::class)
            ->setPublic(true);

        $container->autowire(GeoLocationService::class)
            ->setPublic(true);

        // Application Service
        $container->autowire(AuthenticationApplicationService::class)
            ->setPublic(true);
    }
} 