<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Service;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;

/**
 * Policy-Based Access Control (PBAC) für Plugin-System
 * 
 * Evaluiert komplexe Plugin-Installation Policies basierend auf:
 * - Plugin-Eigenschaften (Source, Risk, Type)
 * - User-Eigenschaften (Role, Department, Trust-Level)  
 * - System-Eigenschaften (Environment, Compliance, Resources)
 * - Business-Regeln (Approval-Workflows, Budgets, etc.)
 */
class PluginPbacService
{
    private array $policies = [];
    
    public function __construct()
    {
        $this->initializePluginPolicies();
    }
    
    /**
     * Plugin-Installation Policy Check
     */
    public function canInstallPlugin(User $user, array $pluginData, array $systemContext = []): array
    {
        $decision = [
            'allowed' => false,
            'decision' => 'deny',
            'reason' => [],
            'requirements' => [],
            'warnings' => [],
            'approval_required' => false,
            'approval_level' => null
        ];
        
        $context = $this->buildPluginContext($user, $pluginData, $systemContext);
        
        // Alle Plugin-Policies evaluieren
        foreach ($this->policies as $policyName => $policy) {
            $result = $this->evaluatePolicy($policy, $context);
            
            if ($result['decision'] === 'deny') {
                $decision['reason'][] = $result['reason'];
                return $decision; // Sofortiger Stopp bei DENY
            }
            
            if ($result['decision'] === 'approval_required') {
                $decision['approval_required'] = true;
                $decision['approval_level'] = max(
                    $decision['approval_level'] ?? 0,
                    $result['approval_level'] ?? 1
                );
                $decision['requirements'] = array_merge(
                    $decision['requirements'],
                    $result['requirements'] ?? []
                );
            }
            
            if (!empty($result['warnings'])) {
                $decision['warnings'] = array_merge($decision['warnings'], $result['warnings']);
            }
        }
        
        // Finale Entscheidung
        if ($decision['approval_required']) {
            $decision['decision'] = 'approval_required';
            $decision['allowed'] = false;
        } else {
            $decision['decision'] = 'allow';
            $decision['allowed'] = true;
        }
        
        return $decision;
    }
    
    /**
     * Plugin-Store spezifische Checks
     */
    public function canInstallFromStore(User $user, string $pluginId, string $storeUrl): array
    {
        // Plugin-Daten aus Store abrufen
        $pluginData = $this->fetchPluginFromStore($pluginId, $storeUrl);
        
        if (!$pluginData) {
            return [
                'allowed' => false,
                'decision' => 'deny',
                'reason' => ['Plugin not found in store'],
                'requirements' => [],
                'warnings' => []
            ];
        }
        
        $systemContext = [
            'source' => 'plugin_store',
            'store_url' => $storeUrl,
            'store_verified' => $this->isVerifiedStore($storeUrl),
            'plugin_verified' => $pluginData['verified'] ?? false,
            'plugin_rating' => $pluginData['rating'] ?? 0,
            'download_count' => $pluginData['downloads'] ?? 0
        ];
        
        return $this->canInstallPlugin($user, $pluginData, $systemContext);
    }
    
    private function evaluatePolicy(array $policy, array $context): array
    {
        $policyType = $policy['type'];
        
        return match($policyType) {
            'source_trust' => $this->evaluateSourceTrustPolicy($policy, $context),
            'risk_assessment' => $this->evaluateRiskAssessmentPolicy($policy, $context),
            'compliance' => $this->evaluateCompliancePolicy($policy, $context),
            'environment' => $this->evaluateEnvironmentPolicy($policy, $context),
            'user_authorization' => $this->evaluateUserAuthorizationPolicy($policy, $context),
            'resource_limits' => $this->evaluateResourceLimitsPolicy($policy, $context),
            'business_rules' => $this->evaluateBusinessRulesPolicy($policy, $context),
            default => ['decision' => 'allow', 'reason' => 'Unknown policy type']
        };
    }
    
    private function evaluateSourceTrustPolicy(array $policy, array $context): array
    {
        $source = $context['source'] ?? 'unknown';
        $storeVerified = $context['store_verified'] ?? false;
        $pluginVerified = $context['plugin_verified'] ?? false;
        
        // Vertrauensstufen
        $trustLevels = [
            'official_store_verified' => 5,
            'official_store' => 4,
            'verified_third_party' => 3,
            'third_party_store' => 2,
            'custom_upload' => 1,
            'unknown' => 0
        ];
        
        $trustLevel = match($source) {
            'plugin_store' => $storeVerified && $pluginVerified ? 5 : ($storeVerified ? 4 : 2),
            'internal' => 5,
            'custom' => 1,
            default => 0
        };
        
        $requiredTrust = $policy['min_trust_level'] ?? 3;
        
        if ($trustLevel < $requiredTrust) {
            if ($trustLevel >= 2) {
                return [
                    'decision' => 'approval_required',
                    'approval_level' => $requiredTrust - $trustLevel,
                    'reason' => "Plugin source trust level too low (current: {$trustLevel}, required: {$requiredTrust})",
                    'requirements' => ['Manual security review required']
                ];
            } else {
                return [
                    'decision' => 'deny',
                    'reason' => 'Plugin source not trusted'
                ];
            }
        }
        
        return ['decision' => 'allow'];
    }
    
    private function evaluateRiskAssessmentPolicy(array $policy, array $context): array
    {
        $plugin = $context['plugin'];
        $riskFactors = 0;
        $warnings = [];
        
        // Risiko-Faktoren bewerten
        if (!empty($plugin['permissions']['database'])) {
            $riskFactors += 2;
            $warnings[] = 'Plugin requires database access';
        }
        
        if (!empty($plugin['permissions']['filesystem'])) {
            $riskFactors += 2;
            $warnings[] = 'Plugin requires filesystem access';
        }
        
        if (!empty($plugin['permissions']['network'])) {
            $riskFactors += 1;
            $warnings[] = 'Plugin makes network requests';
        }
        
        if (!empty($plugin['permissions']['admin'])) {
            $riskFactors += 3;
            $warnings[] = 'Plugin requires admin privileges';
        }
        
        // Native Code = höheres Risiko
        if ($plugin['type'] === 'native' || !empty($plugin['contains_binary'])) {
            $riskFactors += 2;
            $warnings[] = 'Plugin contains compiled code';
        }
        
        $maxRiskLevel = $policy['max_risk_level'] ?? 5;
        
        if ($riskFactors > $maxRiskLevel) {
            return [
                'decision' => 'approval_required',
                'approval_level' => min(3, ceil($riskFactors / 3)),
                'reason' => "Plugin risk level too high (score: {$riskFactors}, max: {$maxRiskLevel})",
                'requirements' => ['Security team approval required'],
                'warnings' => $warnings
            ];
        }
        
        return [
            'decision' => 'allow',
            'warnings' => $warnings
        ];
    }
    
    private function evaluateCompliancePolicy(array $policy, array $context): array
    {
        $plugin = $context['plugin'];
        $userDepartment = $context['user']['department'] ?? null;
        $environment = $context['environment'] ?? 'production';
        
        $complianceIssues = [];
        
        // GDPR-Compliance für EU-Daten
        if ($context['system']['gdpr_required'] ?? false) {
            if (!($plugin['compliance']['gdpr'] ?? false)) {
                $complianceIssues[] = 'Plugin not GDPR compliant';
            }
        }
        
        // SOX-Compliance für Finanz-Abteilung
        if ($userDepartment === 'finance' && $environment === 'production') {
            if (!($plugin['compliance']['sox'] ?? false)) {
                $complianceIssues[] = 'Plugin not SOX compliant';
            }
        }
        
        // PCI-DSS für Payment-Plugins
        if (str_contains(strtolower($plugin['category'] ?? ''), 'payment')) {
            if (!($plugin['compliance']['pci_dss'] ?? false)) {
                $complianceIssues[] = 'Payment plugin not PCI-DSS compliant';
            }
        }
        
        if (!empty($complianceIssues)) {
            return [
                'decision' => 'approval_required',
                'approval_level' => 3,
                'reason' => 'Compliance issues detected',
                'requirements' => ['Compliance officer approval required'],
                'warnings' => $complianceIssues
            ];
        }
        
        return ['decision' => 'allow'];
    }
    
    private function evaluateEnvironmentPolicy(array $policy, array $context): array
    {
        $environment = $context['environment'] ?? 'production';
        $plugin = $context['plugin'];
        
        // Produktions-Umgebung = strengere Regeln
        if ($environment === 'production') {
            // Nur stable Versionen in Production
            if (($plugin['version_stability'] ?? 'stable') !== 'stable') {
                return [
                    'decision' => 'deny',
                    'reason' => 'Only stable plugin versions allowed in production'
                ];
            }
            
            // Mindest-Download-Count für Produktion
            $minDownloads = $policy['production_min_downloads'] ?? 1000;
            if (($context['download_count'] ?? 0) < $minDownloads) {
                return [
                    'decision' => 'approval_required',
                    'approval_level' => 2,
                    'reason' => 'Plugin has insufficient download count for production',
                    'requirements' => ['QA team testing required']
                ];
            }
        }
        
        return ['decision' => 'allow'];
    }
    
    private function evaluateUserAuthorizationPolicy(array $policy, array $context): array
    {
        $user = $context['user'];
        $plugin = $context['plugin'];
        
        // Department-spezifische Plugin-Restrictions
        $departmentRestrictions = $policy['department_restrictions'] ?? [];
        $userDept = $user['department'] ?? null;
        
        if (isset($departmentRestrictions[$userDept])) {
            $allowedCategories = $departmentRestrictions[$userDept]['allowed_categories'] ?? [];
            $pluginCategory = $plugin['category'] ?? 'other';
            
            if (!empty($allowedCategories) && !in_array($pluginCategory, $allowedCategories)) {
                return [
                    'decision' => 'deny',
                    'reason' => "Plugin category '{$pluginCategory}' not allowed for department '{$userDept}'"
                ];
            }
        }
        
        // Budget-Limits für kostenpflichtige Plugins
        if (($plugin['price'] ?? 0) > 0) {
            $userBudgetLimit = $user['plugin_budget_limit'] ?? 0;
            if ($plugin['price'] > $userBudgetLimit) {
                return [
                    'decision' => 'approval_required',
                    'approval_level' => 2,
                    'reason' => 'Plugin price exceeds user budget limit',
                    'requirements' => ['Budget approval required']
                ];
            }
        }
        
        return ['decision' => 'allow'];
    }
    
    private function evaluateResourceLimitsPolicy(array $policy, array $context): array
    {
        $plugin = $context['plugin'];
        $systemResources = $context['system']['resources'] ?? [];
        
        // Resource-Requirements prüfen
        $requirements = $plugin['requirements'] ?? [];
        
        if (isset($requirements['memory'])) {
            $availableMemory = $systemResources['available_memory'] ?? 0;
            if ($requirements['memory'] > $availableMemory) {
                return [
                    'decision' => 'deny',
                    'reason' => 'Insufficient system memory for plugin'
                ];
            }
        }
        
        if (isset($requirements['disk_space'])) {
            $availableDisk = $systemResources['available_disk'] ?? 0;
            if ($requirements['disk_space'] > $availableDisk) {
                return [
                    'decision' => 'deny',
                    'reason' => 'Insufficient disk space for plugin'
                ];
            }
        }
        
        return ['decision' => 'allow'];
    }
    
    private function evaluateBusinessRulesPolicy(array $policy, array $context): array
    {
        $plugin = $context['plugin'];
        $user = $context['user'];
        
        // Maximale Anzahl Plugins pro User/Department
        $maxPluginsPerUser = $policy['max_plugins_per_user'] ?? 50;
        $currentPluginCount = $user['current_plugin_count'] ?? 0;
        
        if ($currentPluginCount >= $maxPluginsPerUser) {
            return [
                'decision' => 'approval_required',
                'approval_level' => 1,
                'reason' => 'User has reached maximum plugin limit',
                'requirements' => ['Plugin cleanup or limit increase required']
            ];
        }
        
        // Konkurrenz-Check (nur ein Plugin pro Kategorie)
        $exclusiveCategories = $policy['exclusive_categories'] ?? ['payment', 'analytics'];
        $pluginCategory = $plugin['category'] ?? 'other';
        
        if (in_array($pluginCategory, $exclusiveCategories)) {
            $hasConflict = $user['installed_categories'][$pluginCategory] ?? false;
            if ($hasConflict) {
                return [
                    'decision' => 'approval_required',
                    'approval_level' => 2,
                    'reason' => "Plugin conflicts with existing {$pluginCategory} plugin",
                    'requirements' => ['Remove existing plugin or get conflict resolution approval']
                ];
            }
        }
        
        return ['decision' => 'allow'];
    }
    
    private function buildPluginContext(User $user, array $pluginData, array $systemContext): array
    {
        return [
            'user' => [
                'id' => $user->getId()->value(),
                'department' => $user->getDepartment(),
                'roles' => $user->getRoles(),
                'plugin_budget_limit' => $user->getPluginBudgetLimit() ?? 1000,
                'current_plugin_count' => $this->getUserPluginCount($user),
                'installed_categories' => $this->getUserInstalledCategories($user)
            ],
            'plugin' => $pluginData,
            'system' => array_merge([
                'environment' => $_ENV['APP_ENV'] ?? 'production',
                'gdpr_required' => true,
                'resources' => $this->getSystemResources()
            ], $systemContext),
            'source' => $systemContext['source'] ?? 'unknown',
            'store_verified' => $systemContext['store_verified'] ?? false,
            'plugin_verified' => $systemContext['plugin_verified'] ?? false,
            'download_count' => $systemContext['download_count'] ?? 0
        ];
    }
    
    private function initializePluginPolicies(): void
    {
        $this->policies = [
            'source_trust' => [
                'type' => 'source_trust',
                'min_trust_level' => 3,
                'description' => 'Validates plugin source trustworthiness'
            ],
            'risk_assessment' => [
                'type' => 'risk_assessment',
                'max_risk_level' => 5,
                'description' => 'Assesses plugin security and operational risks'
            ],
            'compliance' => [
                'type' => 'compliance',
                'description' => 'Ensures regulatory compliance (GDPR, SOX, PCI-DSS)'
            ],
            'environment' => [
                'type' => 'environment',
                'production_min_downloads' => 1000,
                'description' => 'Environment-specific installation rules'
            ],
            'user_authorization' => [
                'type' => 'user_authorization',
                'department_restrictions' => [
                    'hr' => ['allowed_categories' => ['hr', 'communication', 'reporting']],
                    'finance' => ['allowed_categories' => ['finance', 'reporting', 'analytics']],
                    'it' => ['allowed_categories' => []] // IT kann alles
                ],
                'description' => 'User and department-based authorization rules'
            ],
            'resource_limits' => [
                'type' => 'resource_limits',
                'description' => 'System resource availability checks'
            ],
            'business_rules' => [
                'type' => 'business_rules',
                'max_plugins_per_user' => 25,
                'exclusive_categories' => ['payment', 'analytics', 'backup'],
                'description' => 'Business logic and operational rules'
            ]
        ];
    }
    
    // Helper methods (würden echte Implementierungen benötigen)
    private function fetchPluginFromStore(string $pluginId, string $storeUrl): ?array
    {
        // Plugin-Store API Call
        return [
            'id' => $pluginId,
            'name' => 'Example Plugin',
            'version' => '1.0.0',
            'category' => 'productivity',
            'price' => 0,
            'verified' => true,
            'rating' => 4.5,
            'permissions' => ['database', 'network'],
            'compliance' => ['gdpr' => true, 'sox' => false],
            'requirements' => ['memory' => 64, 'disk_space' => 100]
        ];
    }
    
    private function isVerifiedStore(string $storeUrl): bool
    {
        $verifiedStores = [
            'https://plugins.companyos.io',
            'https://marketplace.symfony.com'
        ];
        return in_array($storeUrl, $verifiedStores);
    }
    
    private function getUserPluginCount(User $user): int
    {
        // Count user's installed plugins
        return 5; // Mock
    }
    
    private function getUserInstalledCategories(User $user): array
    {
        // Get categories of user's installed plugins
        return ['productivity' => true]; // Mock
    }
    
    private function getSystemResources(): array
    {
        return [
            'available_memory' => 1024,
            'available_disk' => 10000,
            'cpu_cores' => 4
        ];
    }
} 