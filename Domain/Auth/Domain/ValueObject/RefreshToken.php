<?php

namespace CompanyOS\Domain\Auth\Domain\ValueObject;

class RefreshToken
{
    public function __construct(
        private string $token,
        private \DateTimeImmutable $expiresAt,
        private string $accessTokenId
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

    public function getAccessTokenId(): string
    {
        return $this->accessTokenId;
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