<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Infrastructure\DependencyInjection;

use CompanyOS\Application\Auth\CommandHandler\ChangePasswordCommandHandler;
use CompanyOS\Application\Auth\CommandHandler\LoginUserCommandHandler;
use CompanyOS\Application\Auth\CommandHandler\LogoutUserCommandHandler;
use CompanyOS\Application\Auth\CommandHandler\RefreshTokenCommandHandler;
use CompanyOS\Application\Auth\CommandHandler\RequestPasswordResetCommandHandler;
use CompanyOS\Application\Auth\CommandHandler\ResetPasswordCommandHandler;
use CompanyOS\Application\Auth\EventHandler\AuthenticationEventHandler;
use CompanyOS\Application\Auth\QueryHandler\GetActiveSessionsQueryHandler;
use CompanyOS\Application\Auth\QueryHandler\GetOAuthClientsQueryHandler;
use CompanyOS\Application\Auth\QueryHandler\GetUserProfileQueryHandler;
use CompanyOS\Application\Auth\QueryHandler\ValidateTokenQueryHandler;
use CompanyOS\Application\Auth\Service\AuthenticationApplicationService;
use CompanyOS\Domain\Auth\Domain\Repository\AccessTokenRepositoryInterface;
use CompanyOS\Domain\Auth\Domain\Repository\ClientRepositoryInterface;
use CompanyOS\Domain\Auth\Infrastructure\Event\AuthenticationEventDispatcher;
use CompanyOS\Domain\Auth\Infrastructure\Event\AuthenticationEventSubscriber;
use CompanyOS\Domain\Auth\Infrastructure\External\EmailService;
use CompanyOS\Domain\Auth\Infrastructure\External\GeoLocationService;
use CompanyOS\Domain\Auth\Infrastructure\External\NotificationService;
use CompanyOS\Domain\Auth\Infrastructure\External\SecurityAuditService;
use CompanyOS\Domain\Auth\Infrastructure\Persistence\DoctrineAccessTokenRepository;
use CompanyOS\Domain\Auth\Infrastructure\Persistence\DoctrineClientRepository;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;
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