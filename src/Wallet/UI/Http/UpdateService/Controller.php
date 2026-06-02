<?php

declare(strict_types=1);

namespace App\Wallet\UI\Http\UpdateService;

use App\Wallet\Application\Command\UpdateBalance;
use App\Wallet\Application\Service\UpdateBalanceService;
use App\Wallet\Domain\Exception\DomainException;
use App\Wallet\Domain\Money;
use App\Wallet\Domain\Transaction\TransactionReason;
use App\Wallet\Domain\Transaction\TransactionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/v1')]
final readonly class Controller
{
    public function __construct(
        private SerializerInterface $serializer,
        private UpdateBalanceService $service,
    ) {}

    #[Route(
        path: '/balance/{id}/{type}/{reason}',
        requirements: [
            'id' => Requirement::POSITIVE_INT,
        ],
        methods: ['POST'],
        format: 'json',
    )]
    public function __invoke(
        int $id,
        TransactionType $type,
        TransactionReason $reason,
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)]
        RequestBody $requestBody,
    ): Response {
        $command = new UpdateBalance(
            id: $id,
            type: $type,
            reason: $reason,
            amount: new Money(amount: $requestBody->amount, currency: $requestBody->currency),
        );

        try {
            $this->service->update($command);
        } catch (DomainException $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = $this->serializer->serialize(true, 'json');

        return JsonResponse::fromJsonString($data);
    }
}
