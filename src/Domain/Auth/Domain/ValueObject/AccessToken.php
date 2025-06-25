<?php

namespace CompanyOS\Domain\Auth\Domain\ValueObject;

class AccessToken
{
    public function __construct(
        private string $token,
        private \DateTimeImmutable $expiresAt,
        private string $userId
    ) {
        if (empty($token)) {
            throw new \InvalidArgumentException('Token cannot be empty');
        }
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }
} 