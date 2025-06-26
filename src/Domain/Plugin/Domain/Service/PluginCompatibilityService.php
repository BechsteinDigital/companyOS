<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Service;

final class PluginCompatibilityService
{
    private const MIN_PHP_VERSION = '8.1.0';
    private const MIN_SYMFONY_VERSION = '6.0.0';

    public function checkCompatibility(
        string $pluginName,
        string $version,
        array $systemRequirements
    ): array {
        $issues = [];
        $warnings = [];
        $recommendations = [];

        // PHP Version Check
        if (isset($systemRequirements['php'])) {
            if (!$this->isPhpVersionCompatible($systemRequirements['php'])) {
                $issues[] = sprintf(
                    'PHP Version %s required, but %s is installed',
                    $systemRequirements['php'],
                    PHP_VERSION
                );
            }
        }

        // Symfony Version Check
        if (isset($systemRequirements['symfony'])) {
            if (!$this->isSymfonyVersionCompatible($systemRequirements['symfony'])) {
                $issues[] = sprintf(
                    'Symfony Version %s required, but %s is installed',
                    $systemRequirements['symfony'],
                    $this->getSymfonyVersion()
                );
            }
        }

        // Extension Checks
        if (isset($systemRequirements['extensions'])) {
            foreach ($systemRequirements['extensions'] as $extension) {
                if (!extension_loaded($extension)) {
                    $issues[] = sprintf('PHP Extension "%s" is required but not installed', $extension);
                }
            }
        }

        // Directory Permissions Check
        if (isset($systemRequirements['directories'])) {
            foreach ($systemRequirements['directories'] as $directory) {
                if (!$this->isDirectoryWritable($directory)) {
                    $issues[] = sprintf('Directory "%s" is not writable', $directory);
                }
            }
        }

        // Memory Limit Check
        if (isset($systemRequirements['memory_limit'])) {
            if (!$this->isMemoryLimitSufficient($systemRequirements['memory_limit'])) {
                $warnings[] = sprintf(
                    'Memory limit should be at least %s, current: %s',
                    $systemRequirements['memory_limit'],
                    ini_get('memory_limit')
                );
            }
        }

        // Disk Space Check
        if (isset($systemRequirements['disk_space'])) {
            if (!$this->isDiskSpaceSufficient($systemRequirements['disk_space'])) {
                $issues[] = sprintf(
                    'Insufficient disk space. Required: %s, Available: %s',
                    $this->formatBytes($systemRequirements['disk_space']),
                    $this->formatBytes($this->getAvailableDiskSpace())
                );
            }
        }

        // Security Recommendations
        if ($this->isDevelopmentEnvironment()) {
            $recommendations[] = 'Consider using a production environment for better security';
        }

        if (!$this->isSslEnabled()) {
            $warnings[] = 'SSL is not enabled. Consider enabling HTTPS for security';
        }

        $isCompatible = empty($issues);

        return [
            'isCompatible' => $isCompatible,
            'issues' => $issues,
            'warnings' => $warnings,
            'recommendations' => $recommendations,
            'systemInfo' => $this->getSystemInfo()
        ];
    }

    private function isPhpVersionCompatible(string $requiredVersion): bool
    {
        return version_compare(PHP_VERSION, $requiredVersion, '>=');
    }

    private function isSymfonyVersionCompatible(string $requiredVersion): bool
    {
        $currentVersion = $this->getSymfonyVersion();
        return version_compare($currentVersion, $requiredVersion, '>=');
    }

    private function getSymfonyVersion(): string
    {
        if (class_exists('Symfony\Component\HttpKernel\Kernel')) {
            return \Symfony\Component\HttpKernel\Kernel::VERSION;
        }
        return '0.0.0';
    }

    private function isDirectoryWritable(string $directory): bool
    {
        $fullPath = realpath($directory);
        return $fullPath && is_writable($fullPath);
    }

    private function isMemoryLimitSufficient(string $requiredLimit): bool
    {
        $currentLimit = ini_get('memory_limit');
        return $this->compareMemoryLimits($currentLimit, $requiredLimit) >= 0;
    }

    private function compareMemoryLimits(string $current, string $required): int
    {
        $currentBytes = $this->parseMemoryLimit($current);
        $requiredBytes = $this->parseMemoryLimit($required);
        return $currentBytes <=> $requiredBytes;
    }

    private function parseMemoryLimit(string $limit): int
    {
        $unit = strtolower(substr($limit, -1));
        $value = (int) substr($limit, 0, -1);

        return match($unit) {
            'k' => $value * 1024,
            'm' => $value * 1024 * 1024,
            'g' => $value * 1024 * 1024 * 1024,
            default => $value
        };
    }

    private function isDiskSpaceSufficient(int $requiredBytes): bool
    {
        $availableBytes = $this->getAvailableDiskSpace();
        return $availableBytes >= $requiredBytes;
    }

    private function getAvailableDiskSpace(): int
    {
        $path = dirname(__DIR__, 4); // Project root
        return disk_free_space($path) ?: 0;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function isDevelopmentEnvironment(): bool
    {
        return in_array($_SERVER['APP_ENV'] ?? 'prod', ['dev', 'test']);
    }

    private function isSslEnabled(): bool
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }

    private function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'symfony_version' => $this->getSymfonyVersion(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'extensions' => get_loaded_extensions(),
            'disk_free_space' => $this->formatBytes($this->getAvailableDiskSpace()),
            'environment' => $_SERVER['APP_ENV'] ?? 'prod',
            'ssl_enabled' => $this->isSslEnabled()
        ];
    }
} 