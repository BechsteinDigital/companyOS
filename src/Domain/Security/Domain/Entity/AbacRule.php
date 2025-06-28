<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Domain\Security\Domain\Entity;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'abac_rules')]
#[ORM\Index(name: 'idx_abac_permission', columns: ['permission'])]
#[ORM\Index(name: 'idx_abac_priority', columns: ['priority'])]
class AbacRule
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100)]
    private string $permission;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'json')]
    private array $conditions;

    #[ORM\Column(type: 'string', length: 20)]
    private string $effect; // 'allow' or 'deny'

    #[ORM\Column(type: 'integer')]
    private int $priority;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        string $name,
        string $permission,
        string $description,
        array $conditions,
        string $effect,
        int $priority = 0,
        bool $isActive = true,
        ?array $metadata = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->permission = $permission;
        $this->description = $description;
        $this->conditions = $conditions;
        $this->effect = $effect;
        $this->priority = $priority;
        $this->isActive = $isActive;
        $this->metadata = $metadata;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPermission(): string
    {
        return $this->permission;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getEffect(): string
    {
        return $this->effect;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function isAllow(): bool
    {
        return $this->effect === 'allow';
    }

    public function isDeny(): bool
    {
        return $this->effect === 'deny';
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateRule(
        string $name,
        string $description,
        array $conditions,
        string $effect,
        int $priority
    ): void {
        $this->name = $name;
        $this->description = $description;
        $this->conditions = $conditions;
        $this->effect = $effect;
        $this->priority = $priority;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Evaluiert die Regel-Bedingungen gegen einen Kontext
     */
    public function evaluate(array $context): bool
    {
        if (!$this->isActive) {
            return false;
        }

        return $this->evaluateConditions($this->conditions, $context);
    }

    private function evaluateConditions(array $conditions, array $context): bool
    {
        foreach ($conditions as $field => $condition) {
            if (is_array($condition)) {
                // Nested conditions (AND/OR logic)
                if ($field === '$and') {
                    foreach ($condition as $subCondition) {
                        if (!$this->evaluateConditions($subCondition, $context)) {
                            return false;
                        }
                    }
                } elseif ($field === '$or') {
                    $anyTrue = false;
                    foreach ($condition as $subCondition) {
                        if ($this->evaluateConditions($subCondition, $context)) {
                            $anyTrue = true;
                            break;
                        }
                    }
                    if (!$anyTrue) {
                        return false;
                    }
                } else {
                    // Field-specific conditions
                    if (!$this->evaluateFieldCondition($field, $condition, $context)) {
                        return false;
                    }
                }
            } else {
                // Simple equality check
                if (!isset($context[$field]) || $context[$field] !== $condition) {
                    return false;
                }
            }
        }

        return true;
    }

    private function evaluateFieldCondition(string $field, array $condition, array $context): bool
    {
        $value = $context[$field] ?? null;

        foreach ($condition as $operator => $expectedValue) {
            switch ($operator) {
                case '$eq':
                    if ($value !== $expectedValue) return false;
                    break;
                case '$ne':
                    if ($value === $expectedValue) return false;
                    break;
                case '$in':
                    if (!in_array($value, $expectedValue, true)) return false;
                    break;
                case '$nin':
                    if (in_array($value, $expectedValue, true)) return false;
                    break;
                case '$gt':
                    if ($value <= $expectedValue) return false;
                    break;
                case '$gte':
                    if ($value < $expectedValue) return false;
                    break;
                case '$lt':
                    if ($value >= $expectedValue) return false;
                    break;
                case '$lte':
                    if ($value > $expectedValue) return false;
                    break;
                case '$regex':
                    if (!preg_match($expectedValue, (string) $value)) return false;
                    break;
                case '$between':
                    if ($value < $expectedValue[0] || $value > $expectedValue[1]) return false;
                    break;
                default:
                    throw new \InvalidArgumentException("Unknown operator: $operator");
            }
        }

        return true;
    }
} 