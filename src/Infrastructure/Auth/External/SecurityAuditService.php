<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Infrastructure\External;

use CompanyOS\Domain\ValueObject\Uuid;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

final class SecurityAuditService
{
    private const MAX_FAILED_ATTEMPTS = 5;
    private const BLOCK_DURATION_MINUTES = 15;
    private const UNUSUAL_LOGIN_DISTANCE_KM = 100;

    public function __construct(
        private readonly Connection $connection,
        private readonly LoggerInterface $logger
    ) {
    }

    public function recordSuccessfulLogin(
        string $userId,
        string $email,
        string $ipAddress,
        string $userAgent
    ): void {
        $sql = 'INSERT INTO auth_login_audit (user_id, email, ip_address, user_agent, success, created_at) 
                VALUES (:userId, :email, :ipAddress, :userAgent, :success, :createdAt)';

        $this->connection->executeStatement($sql, [
            'userId' => $userId,
            'email' => $email,
            'ipAddress' => $ipAddress,
            'userAgent' => $userAgent,
            'success' => true,
            'createdAt' => new \DateTimeImmutable()
        ]);

        $this->logger->info('Successful login recorded', [
            'userId' => $userId,
            'email' => $email,
            'ipAddress' => $ipAddress
        ]);
    }

    public function recordFailedLogin(
        string $email,
        string $ipAddress,
        string $userAgent,
        string $reason
    ): void {
        $sql = 'INSERT INTO auth_login_audit (email, ip_address, user_agent, success, reason, created_at) 
                VALUES (:email, :ipAddress, :userAgent, :success, :reason, :createdAt)';

        $this->connection->executeStatement($sql, [
            'email' => $email,
            'ipAddress' => $ipAddress,
            'userAgent' => $userAgent,
            'success' => false,
            'reason' => $reason,
            'createdAt' => new \DateTimeImmutable()
        ]);

        $this->logger->warning('Failed login recorded', [
            'email' => $email,
            'ipAddress' => $ipAddress,
            'reason' => $reason
        ]);
    }

    public function recordLogout(
        string $userId,
        string $ipAddress,
        string $userAgent
    ): void {
        $sql = 'INSERT INTO auth_logout_audit (user_id, ip_address, user_agent, created_at) 
                VALUES (:userId, :ipAddress, :userAgent, :createdAt)';

        $this->connection->executeStatement($sql, [
            'userId' => $userId,
            'ipAddress' => $ipAddress,
            'userAgent' => $userAgent,
            'createdAt' => new \DateTimeImmutable()
        ]);
    }

    public function shouldBlockIp(string $ipAddress): bool
    {
        $sql = 'SELECT COUNT(*) as failed_attempts 
                FROM auth_login_audit 
                WHERE ip_address = :ipAddress 
                AND success = false 
                AND created_at > :timeThreshold';

        $result = $this->connection->fetchAssociative($sql, [
            'ipAddress' => $ipAddress,
            'timeThreshold' => (new \DateTimeImmutable())->modify('-' . self::BLOCK_DURATION_MINUTES . ' minutes')
        ]);

        return (int) $result['failed_attempts'] >= self::MAX_FAILED_ATTEMPTS;
    }

    public function blockIpAddress(string $ipAddress): void
    {
        $sql = 'INSERT INTO auth_ip_blocks (ip_address, reason, blocked_until, created_at) 
                VALUES (:ipAddress, :reason, :blockedUntil, :createdAt)';

        $this->connection->executeStatement($sql, [
            'ipAddress' => $ipAddress,
            'reason' => 'Too many failed login attempts',
            'blockedUntil' => (new \DateTimeImmutable())->modify('+' . self::BLOCK_DURATION_MINUTES . ' minutes'),
            'createdAt' => new \DateTimeImmutable()
        ]);

        $this->logger->warning('IP address blocked', ['ipAddress' => $ipAddress]);
    }

    public function isIpBlocked(string $ipAddress): bool
    {
        $sql = 'SELECT COUNT(*) as blocked 
                FROM auth_ip_blocks 
                WHERE ip_address = :ipAddress 
                AND blocked_until > :now';

        $result = $this->connection->fetchAssociative($sql, [
            'ipAddress' => $ipAddress,
            'now' => new \DateTimeImmutable()
        ]);

        return (int) $result['blocked'] > 0;
    }

    public function isUnusualLogin(string $ipAddress, Uuid $userId): bool
    {
        // Prüfe, ob der User bereits von dieser IP aus eingeloggt war
        $sql = 'SELECT COUNT(*) as previous_logins 
                FROM auth_login_audit 
                WHERE user_id = :userId 
                AND ip_address = :ipAddress 
                AND success = true';

        $result = $this->connection->fetchAssociative($sql, [
            'userId' => $userId->value(),
            'ipAddress' => $ipAddress
        ]);

        return (int) $result['previous_logins'] === 0;
    }

    public function shouldNotifyUser(string $email): bool
    {
        $sql = 'SELECT COUNT(*) as failed_attempts 
                FROM auth_login_audit 
                WHERE email = :email 
                AND success = false 
                AND created_at > :timeThreshold';

        $result = $this->connection->fetchAssociative($sql, [
            'email' => $email,
            'timeThreshold' => (new \DateTimeImmutable())->modify('-1 hour')
        ]);

        return (int) $result['failed_attempts'] >= 3;
    }

    public function cleanupUserSessions(Uuid $userId): void
    {
        // Lösche abgelaufene Sessions für den User
        $sql = 'DELETE FROM oauth_access_tokens 
                WHERE user_id = :userId 
                AND expiry_date_time < :now';

        $this->connection->executeStatement($sql, [
            'userId' => $userId->value(),
            'now' => new \DateTimeImmutable()
        ]);
    }

    public function getLoginHistory(string $userId, int $limit = 10): array
    {
        $sql = 'SELECT * FROM auth_login_audit 
                WHERE user_id = :userId 
                ORDER BY created_at DESC 
                LIMIT :limit';

        return $this->connection->fetchAllAssociative($sql, [
            'userId' => $userId,
            'limit' => $limit
        ]);
    }
} 