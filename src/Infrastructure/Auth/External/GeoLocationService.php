<?php

declare(strict_types=1);

namespace CompanyOS\Infrastructure\Auth\External;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GeoLocationService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $apiKey = '',
        private readonly string $apiEndpoint = 'http://ip-api.com/json/'
    ) {
    }

    public function getLocationByIp(string $ipAddress): ?array
    {
        try {
            // Für lokale/private IPs
            if ($this->isPrivateIp($ipAddress)) {
                return [
                    'country' => 'Local',
                    'city' => 'Local Network',
                    'region' => 'Local',
                    'timezone' => 'Local',
                    'isp' => 'Local Network'
                ];
            }

            $response = $this->httpClient->request('GET', $this->apiEndpoint . $ipAddress);
            $data = $response->toArray();

            if ($data['status'] === 'success') {
                return [
                    'country' => $data['country'] ?? 'Unknown',
                    'city' => $data['city'] ?? 'Unknown',
                    'region' => $data['regionName'] ?? 'Unknown',
                    'timezone' => $data['timezone'] ?? 'Unknown',
                    'isp' => $data['isp'] ?? 'Unknown',
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null
                ];
            }

            $this->logger->warning('Failed to get location for IP', [
                'ipAddress' => $ipAddress,
                'response' => $data
            ]);

            return null;

        } catch (\Exception $e) {
            $this->logger->error('Error getting location for IP', [
                'ipAddress' => $ipAddress,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    public function isUnusualLocation(string $ipAddress, string $userId): bool
    {
        $location = $this->getLocationByIp($ipAddress);
        
        if (!$location) {
            return false;
        }

        // Hier könnte eine Logik implementiert werden, um ungewöhnliche Standorte zu erkennen
        // z.B. basierend auf vorherigen Logins des Users
        
        return false; // Placeholder
    }

    public function getDistanceBetweenIps(string $ip1, string $ip2): ?float
    {
        $location1 = $this->getLocationByIp($ip1);
        $location2 = $this->getLocationByIp($ip2);

        if (!$location1 || !$location2 || 
            !isset($location1['latitude'], $location1['longitude'], 
                   $location2['latitude'], $location2['longitude'])) {
            return null;
        }

        return $this->calculateDistance(
            $location1['latitude'],
            $location1['longitude'],
            $location2['latitude'],
            $location2['longitude']
        );
    }

    private function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        // Haversine-Formel für Entfernungsberechnung
        $earthRadius = 6371; // Erdradius in km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function isPrivateIp(string $ipAddress): bool
    {
        $privateRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            '127.0.0.0/8',
            '::1/128'
        ];

        foreach ($privateRanges as $range) {
            if ($this->ipInRange($ipAddress, $range)) {
                return true;
            }
        }

        return false;
    }

    private function ipInRange(string $ip, string $range): bool
    {
        if (strpos($ip, ':') !== false) {
            // IPv6
            return $ip === '::1';
        }

        // IPv4
        list($range, $netmask) = explode('/', $range, 2);
        $rangeDecimal = ip2long($range);
        $ipDecimal = ip2long($ip);
        $wildcardDecimal = pow(2, (32 - $netmask)) - 1;
        $netmaskDecimal = ~$wildcardDecimal;

        return (($ipDecimal & $netmaskDecimal) == ($rangeDecimal & $netmaskDecimal));
    }

    public function getCountryByIp(string $ipAddress): ?string
    {
        $location = $this->getLocationByIp($ipAddress);
        return $location['country'] ?? null;
    }

    public function getCityByIp(string $ipAddress): ?string
    {
        $location = $this->getLocationByIp($ipAddress);
        return $location['city'] ?? null;
    }

    public function getTimezoneByIp(string $ipAddress): ?string
    {
        $location = $this->getLocationByIp($ipAddress);
        return $location['timezone'] ?? null;
    }
} 