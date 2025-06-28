<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Document\Controller;

use CompanyOS\Bundle\CoreBundle\Application\Security\Service\HybridAccessControlService;
use CompanyOS\Bundle\CoreBundle\Domain\Document\Domain\Repository\DocumentRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Beispiel: Hybrid Access Control für Dokumente
 */
#[Route('/api/documents')]
class DocumentControllerExample extends AbstractController
{
    public function __construct(
        private readonly HybridAccessControlService $accessControl,
        private readonly DocumentRepositoryInterface $documentRepository
    ) {
    }

    #[Route('/{id}', methods: ['GET'], name: 'api_documents_view')]
    public function viewDocument(string $id): JsonResponse
    {
        $currentUser = $this->getUser();
        $document = $this->documentRepository->findById(Uuid::fromString($id));
        
        if (!$document) {
            return $this->json(['error' => 'Document not found'], 404);
        }
        
        // Hybrid Access Check
        $canView = $this->accessControl->canViewFile($currentUser, $document);
        
        if (!$canView) {
            // Detaillierte Begründung für Debugging
            $accessDetails = $this->accessControl->hasAccessDetailed(
                user: $currentUser,
                permission: 'document.read',
                resource: $document,
                context: [
                    'document.status' => $document->getStatus(),
                    'document.classification' => $document->getClassification()
                ]
            );
            
            return $this->json([
                'error' => 'Access denied',
                'reason' => $accessDetails['reason'],
                'layer' => $accessDetails['layers']
            ], 403);
        }
        
        return $this->json([
            'success' => true,
            'document' => [
                'id' => $document->getId()->value(),
                'title' => $document->getTitle(),
                'content' => $document->getContent(),
                'owner' => $document->getCreatedBy()?->getEmail()->value(),
                'created_at' => $document->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        ]);
    }
    
    #[Route('/{id}', methods: ['PUT'], name: 'api_documents_edit')]
    public function editDocument(string $id, Request $request): JsonResponse
    {
        $currentUser = $this->getUser();
        $document = $this->documentRepository->findById(Uuid::fromString($id));
        
        if (!$document) {
            return $this->json(['error' => 'Document not found'], 404);
        }
        
        // Hybrid Access Check für Bearbeitung
        $canEdit = $this->accessControl->canEditDocument($currentUser, $document);
        
        if (!$canEdit) {
            return $this->json(['error' => 'Edit access denied'], 403);
        }
        
        // Document update logic...
        $data = json_decode($request->getContent(), true);
        $document->updateContent($data['content'] ?? '');
        
        return $this->json(['success' => true, 'message' => 'Document updated']);
    }
    
    #[Route('/{id}/share', methods: ['POST'], name: 'api_documents_share')]
    public function shareDocument(string $id, Request $request): JsonResponse
    {
        $currentUser = $this->getUser();
        $document = $this->documentRepository->findById(Uuid::fromString($id));
        
        if (!$document) {
            return $this->json(['error' => 'Document not found'], 404);
        }
        
        // Prüfen ob User das Dokument teilen darf
        $canShare = $this->accessControl->hasAccess(
            user: $currentUser,
            permission: 'document.share',
            resource: $document,
            context: [
                'share.type' => 'internal', // vs external
                'document.classification' => $document->getClassification()
            ]
        );
        
        if (!$canShare) {
            return $this->json(['error' => 'Share permission denied'], 403);
        }
        
        $shareData = json_decode($request->getContent(), true);
        $targetUserId = $shareData['user_id'];
        $permissions = $shareData['permissions'] ?? ['read'];
        
        // ACL-Entry erstellen (Dokument teilen)
        $aclService = $this->accessControl->getAclService();
        foreach ($permissions as $permission) {
            $aclService->grantPermission(
                user: $this->userRepository->findById(Uuid::fromString($targetUserId)),
                resource: $document,
                permission: $permission,
                grantedBy: $currentUser
            );
        }
        
        return $this->json([
            'success' => true,
            'message' => "Document shared with permissions: " . implode(', ', $permissions)
        ]);
    }
    
    #[Route('/{id}/permissions', methods: ['GET'], name: 'api_documents_permissions')]
    public function getDocumentPermissions(string $id): JsonResponse
    {
        $currentUser = $this->getUser();
        $document = $this->documentRepository->findById(Uuid::fromString($id));
        
        if (!$document) {
            return $this->json(['error' => 'Document not found'], 404);
        }
        
        // Batch-Permission Check für UI
        $permissions = $this->accessControl->checkMultipleAccess(
            user: $currentUser,
            permissions: ['document.read', 'document.write', 'document.delete', 'document.share'],
            resource: $document,
            context: [
                'ui.component' => 'document_toolbar',
                'document.status' => $document->getStatus()
            ]
        );
        
        // Zusätzlich: Welche Layer haben wie entschieden?
        $detailedChecks = [];
        foreach (['document.read', 'document.write', 'document.delete'] as $perm) {
            $detailedChecks[$perm] = $this->accessControl->hasAccessDetailed(
                user: $currentUser,
                permission: $perm,
                resource: $document
            );
        }
        
        return $this->json([
            'permissions' => $permissions,
            'details' => $detailedChecks,
            'user' => [
                'id' => $currentUser->getId()->value(),
                'roles' => $currentUser->getRoles()
            ],
            'document' => [
                'id' => $document->getId()->value(),
                'owner' => $document->getCreatedBy()?->getId()->value(),
                'is_owner' => $document->getCreatedBy()?->getId()->value() === $currentUser->getId()->value(),
                'status' => $document->getStatus(),
                'classification' => $document->getClassification()
            ]
        ]);
    }
} 