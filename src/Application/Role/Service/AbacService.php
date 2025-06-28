<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Service;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;

/**
 * Attribute-Based Access Control (ABAC) Service
 * 
 * Prüft Kontext-sensitive Zugriffsregeln basierend auf:
 * - Benutzer-Attributen (Abteilung, Rolle, ID)
 * - Resource-Attributen (Owner, Status, Typ) 
 * - Umgebungs-Attributen (Zeit, IP, Gerät)
 * - Aktion-Attributen (CRUD-Operation, Kritikalität)
 */
class AbacService
{
    private array $policies = [];
    
    public function __construct()
    {
        $this->initializeDefaultPolicies();
    }
    
    /**
     * Prüft Kontext-Regeln für Permission
     */
    public function checkContextRules(User $user, string $permission, array $context): bool
    {
        // Keine Regeln definiert -> Zugriff erlaubt
        if (!isset($this->policies[$permission])) {
            return true;
        }
        
        $rules = $this->policies[$permission];
        
        // User-Kontext erweitern
        $context = array_merge($context, $this->buildUserContext($user));
        
        // Alle Regeln müssen erfüllt sein (AND-Verknüpfung)
        foreach ($rules as $rule) {
            if (!$this->evaluateRule($rule, $context)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Policy-basierte Regel-Evaluation
     */
    private function evaluateRule(array $rule, array $context): bool
    {
        $type = $rule['type'] ?? 'condition';
        
        return match($type) {
            'condition' => $this->evaluateCondition($rule, $context),
            'time_restriction' => $this->evaluateTimeRestriction($rule, $context),
            'ownership' => $this->evaluateOwnership($rule, $context),
            'department' => $this->evaluateDepartment($rule, $context),
            'custom' => $this->evaluateCustomRule($rule, $context),
            default => true
        };
    }
    
    private function evaluateCondition(array $rule, array $context): bool
    {
        $attribute = $rule['attribute'];
        $operator = $rule['operator'];
        $value = $rule['value'];
        
        $contextValue = $this->getNestedValue($context, $attribute);
        
        return match($operator) {
            '==' => $contextValue == $value,
            '!=' => $contextValue != $value,
            '>' => $contextValue > $value,
            '<' => $contextValue < $value,
            '>=' => $contextValue >= $value,
            '<=' => $contextValue <= $value,
            'in' => in_array($contextValue, (array) $value),
            'not_in' => !in_array($contextValue, (array) $value),
            'contains' => str_contains((string) $contextValue, (string) $value),
            'regex' => preg_match($value, (string) $contextValue),
            default => false
        };
    }
    
    private function evaluateTimeRestriction(array $rule, array $context): bool
    {
        $currentHour = $context['current.hour'] ?? date('H');
        $currentDay = $context['current.day_of_week'] ?? date('N');
        
        // Arbeitszeiten prüfen
        if (isset($rule['working_hours'])) {
            $workingHours = $rule['working_hours'];
            if ($currentHour < $workingHours['start'] || $currentHour > $workingHours['end']) {
                return false;
            }
        }
        
        // Werktage prüfen
        if (isset($rule['working_days'])) {
            $workingDays = $rule['working_days'];
            if (!in_array($currentDay, $workingDays)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function evaluateOwnership(array $rule, array $context): bool
    {
        $userId = $context['user.id'] ?? null;
        $resourceOwnerId = $context['resource.owner_id'] ?? null;
        
        return match($rule['type']) {
            'own_only' => $userId === $resourceOwnerId,
            'not_own' => $userId !== $resourceOwnerId,
            default => true
        };
    }
    
    private function evaluateDepartment(array $rule, array $context): bool
    {
        $userDepartment = $context['user.department'] ?? null;
        $resourceDepartment = $context['resource.department'] ?? null;
        
        return match($rule['restriction']) {
            'same_department' => $userDepartment === $resourceDepartment,
            'allowed_departments' => in_array($userDepartment, $rule['departments']),
            default => true
        };
    }
    
    private function evaluateCustomRule(array $rule, array $context): bool
    {
        // Für komplexe Business-Logic
        $ruleName = $rule['name'];
        
        return match($ruleName) {
            'financial_approval_limit' => $this->checkFinancialLimit($rule, $context),
            'sensitive_data_access' => $this->checkSensitiveDataAccess($rule, $context),
            default => true
        };
    }
    
    private function buildUserContext(User $user): array
    {
        return [
            'user.id' => $user->getId()->value(),
            'user.email' => $user->getEmail()->value(),
            'user.department' => $user->getDepartment() ?? null,
            'user.is_active' => $user->isActive(),
            'user.created_at' => $user->getCreatedAt(),
        ];
    }
    
    private function getNestedValue(array $array, string $key)
    {
        $keys = explode('.', $key);
        $value = $array;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    /**
     * Standard-Policies definieren
     */
    private function initializeDefaultPolicies(): void
    {
        // Beispiel-Policies
        $this->policies = [
            // User können nur eigene Profile bearbeiten
            'user.update' => [
                [
                    'type' => 'ownership',
                    'restriction' => 'own_only'
                ]
            ],
            
            // Sensitive Daten nur während Arbeitszeit
            'user.delete' => [
                [
                    'type' => 'time_restriction',
                    'working_hours' => ['start' => 9, 'end' => 17],
                    'working_days' => [1, 2, 3, 4, 5] // Mo-Fr
                ]
            ],
            
            // Plugin-Installation nur von eigener Abteilung
            'plugin.install' => [
                [
                    'type' => 'department',
                    'restriction' => 'same_department'
                ]
            ]
        ];
    }
    
    /**
     * Dynamische Policy-Verwaltung
     */
    public function addPolicy(string $permission, array $rules): void
    {
        $this->policies[$permission] = $rules;
    }
    
    public function removePolicy(string $permission): void
    {
        unset($this->policies[$permission]);
    }
    
    // Beispiel Custom Rules
    private function checkFinancialLimit(array $rule, array $context): bool
    {
        $userLevel = $context['user.level'] ?? 1;
        $amount = $context['resource.amount'] ?? 0;
        $limits = $rule['limits'] ?? [];
        
        return $amount <= ($limits[$userLevel] ?? 0);
    }
    
    private function checkSensitiveDataAccess(array $rule, array $context): bool
    {
        $userClearance = $context['user.security_clearance'] ?? 'public';
        $resourceLevel = $context['resource.security_level'] ?? 'public';
        
        $levels = ['public', 'internal', 'confidential', 'secret'];
        $userLevelIndex = array_search($userClearance, $levels);
        $resourceLevelIndex = array_search($resourceLevel, $levels);
        
        return $userLevelIndex >= $resourceLevelIndex;
    }
} 