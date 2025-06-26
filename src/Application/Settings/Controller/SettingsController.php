<?php

namespace CompanyOS\Application\Settings\Controller;

use CompanyOS\Application\Settings\Command\InitializeCompanySettingsCommand;
use CompanyOS\Application\Settings\Command\UpdateCompanySettingsCommand;
use CompanyOS\Application\Settings\DTO\CompanySettingsResponse;
use CompanyOS\Application\Settings\DTO\InitializeCompanySettingsRequest;
use CompanyOS\Application\Settings\DTO\UpdateCompanySettingsRequest;
use CompanyOS\Application\Settings\Query\GetCompanySettingsQuery;
use CompanyOS\Domain\ValueObject\Email;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Settings')]
class SettingsController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get company settings',
        description: 'Retrieve the current company settings'
    )]
    #[OA\Response(
        response: 200,
        description: 'Settings retrieved successfully',
        content: new OA\JsonContent(ref: new Model(type: CompanySettingsResponse::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Settings not found'
    )]
    public function getSettings(): JsonResponse
    {
        $envelope = $this->queryBus->dispatch(new GetCompanySettingsQuery());
        $settings = $envelope->last(HandledStamp::class)?->getResult();
        if (!$settings) {
            return $this->json(['error' => 'Settings not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json(CompanySettingsResponse::fromEntity($settings));
    }

    #[Route('/initialize', methods: ['POST'])]
    #[OA\Post(
        summary: 'Initialize company settings',
        description: 'Initialize the company settings with basic information'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/InitializeCompanySettingsRequest')
    )]
    #[OA\Response(
        response: 201,
        description: 'Settings initialized successfully'
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid request data'
    )]
    #[OA\Response(
        response: 409,
        description: 'Settings already exist'
    )]
    public function initializeSettings(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new InitializeCompanySettingsRequest(
            $data['companyName'] ?? '',
            $data['street'] ?? '',
            $data['houseNumber'] ?? '',
            $data['postalCode'] ?? '',
            $data['city'] ?? '',
            $data['country'] ?? '',
            $data['email'] ?? '',
            $data['smtpHost'] ?? '',
            $data['emailFromAddress'] ?? '',
            $data['emailFromName'] ?? ''
        );
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        try {
            $command = new InitializeCompanySettingsCommand(
                $dto->companyName,
                $dto->street,
                $dto->houseNumber,
                $dto->postalCode,
                $dto->city,
                $dto->country,
                new Email($dto->email),
                $dto->smtpHost,
                new Email($dto->emailFromAddress),
                $dto->emailFromName
            );
            $this->commandBus->dispatch($command);
            return $this->json(['message' => 'Settings initialized successfully'], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    #[Route('', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Update company settings',
        description: 'Update the company settings'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateCompanySettingsRequest')
    )]
    #[OA\Response(
        response: 200,
        description: 'Settings updated successfully'
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid request data'
    )]
    #[OA\Response(
        response: 404,
        description: 'Settings not found'
    )]
    public function updateSettings(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new UpdateCompanySettingsRequest(
            $data['companyName'] ?? null,
            $data['legalName'] ?? null,
            $data['taxNumber'] ?? null,
            $data['vatNumber'] ?? null,
            $data['street'] ?? null,
            $data['houseNumber'] ?? null,
            $data['postalCode'] ?? null,
            $data['city'] ?? null,
            $data['country'] ?? null,
            $data['state'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['fax'] ?? null,
            $data['website'] ?? null,
            $data['supportEmail'] ?? null,
            $data['defaultLanguage'] ?? null,
            $data['defaultCurrency'] ?? null,
            $data['timezone'] ?? null,
            $data['dateFormat'] ?? null,
            $data['timeFormat'] ?? null,
            $data['numberFormat'] ?? null,
            $data['systemName'] ?? null,
            $data['logoUrl'] ?? null,
            $data['defaultUserRole'] ?? null,
            $data['sessionTimeout'] ?? null,
            $data['maintenanceMode'] ?? null,
            $data['emailFromName'] ?? null,
            $data['emailFromAddress'] ?? null,
            $data['emailReplyTo'] ?? null,
            $data['smtpHost'] ?? null,
            $data['smtpPort'] ?? null,
            $data['smtpEncryption'] ?? null,
            $data['smtpUsername'] ?? null,
            $data['smtpPassword'] ?? null,
            $data['salutations'] ?? null
        );
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        try {
            $command = new UpdateCompanySettingsCommand(
                $dto->companyName,
                $dto->legalName,
                $dto->taxNumber,
                $dto->vatNumber,
                $dto->street,
                $dto->houseNumber,
                $dto->postalCode,
                $dto->city,
                $dto->country,
                $dto->state,
                $dto->email ? new Email($dto->email) : null,
                $dto->phone,
                $dto->fax,
                $dto->website,
                $dto->supportEmail ? new Email($dto->supportEmail) : null,
                $dto->defaultLanguage,
                $dto->defaultCurrency,
                $dto->timezone,
                $dto->dateFormat,
                $dto->timeFormat,
                $dto->numberFormat,
                $dto->systemName,
                $dto->logoUrl,
                $dto->defaultUserRole,
                $dto->sessionTimeout,
                $dto->maintenanceMode,
                $dto->emailFromName,
                $dto->emailFromAddress ? new Email($dto->emailFromAddress) : null,
                $dto->emailReplyTo ? new Email($dto->emailReplyTo) : null,
                $dto->smtpHost,
                $dto->smtpPort,
                $dto->smtpEncryption,
                $dto->smtpUsername,
                $dto->smtpPassword,
                $dto->salutations
            );
            $this->commandBus->dispatch($command);
            return $this->json(['message' => 'Settings updated successfully']);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/salutations', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get available salutations',
        description: 'Get all available salutation templates'
    )]
    #[OA\Response(
        response: 200,
        description: 'Salutations retrieved successfully',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'salutations', type: 'object')
            ]
        )
    )]
    public function getSalutations(): JsonResponse
    {
        $envelope = $this->queryBus->dispatch(new GetCompanySettingsQuery());
        $settings = $envelope->last(HandledStamp::class)?->getResult();
        if (!$settings) {
            return $this->json(['error' => 'Settings not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json(['salutations' => $settings->getSalutations()]);
    }

    #[Route('/salutations/{type}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get specific salutation',
        description: 'Get a specific salutation template by type'
    )]
    #[OA\Parameter(
        name: 'type',
        description: 'Salutation type',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Salutation retrieved successfully',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'type', type: 'string'),
                new OA\Property(property: 'template', type: 'string')
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Salutation not found'
    )]
    public function getSalutation(string $type): JsonResponse
    {
        $envelope = $this->queryBus->dispatch(new GetCompanySettingsQuery());
        $settings = $envelope->last(HandledStamp::class)?->getResult();
        if (!$settings) {
            return $this->json(['error' => 'Settings not found'], Response::HTTP_NOT_FOUND);
        }
        $template = $settings->getSalutation($type);
        if (!$template) {
            return $this->json(['error' => 'Salutation not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json(['type' => $type, 'template' => $template]);
    }
} 