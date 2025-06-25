<?php

namespace CompanyOS\Infrastructure\Webhook\Service;

use CompanyOS\Domain\Webhook\Domain\Repository\WebhookRepositoryInterface;
use CompanyOS\Domain\Event\DomainEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;

class WebhookDispatcher
{
    public function __construct(
        private WebhookRepositoryInterface $webhookRepository,
        private LoggerInterface $logger
    ) {
    }

    public function dispatch(DomainEvent $event): void
    {
        $webhooks = $this->webhookRepository->findByEvent($event->getEventName());
        if (empty($webhooks)) {
            return;
        }

        $payload = json_encode([
            'event' => $event->toArray(),
            'webhook' => [
                'timestamp' => (new \DateTimeImmutable())->format('c'),
                'source' => 'companyos',
                'version' => '1.0'
            ]
        ]);

        $client = HttpClient::create();

        foreach ($webhooks as $webhook) {
            try {
                $headers = ['Content-Type' => 'application/json'];
                $signature = $webhook->generateSignature($payload);
                if ($signature) {
                    $headers['X-Webhook-Signature'] = $signature;
                }
                // Async HTTP POST
                $client->request('POST', $webhook->getUrl(), [
                    'headers' => $headers,
                    'body' => $payload,
                    'timeout' => 5,
                ]);
                $this->logger->info('Webhook dispatched', [
                    'url' => $webhook->getUrl(),
                    'event' => $event->getEventName()
                ]);
            } catch (\Throwable $e) {
                $this->logger->error('Webhook dispatch failed', [
                    'url' => $webhook->getUrl(),
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
} 