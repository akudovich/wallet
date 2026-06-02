<?php

declare(strict_types=1);

namespace App\Wallet\UI\Http;

use App\Wallet\Application\Service\GetBalanceService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/v1')]
final class GetBalanceController
{
    public function __construct(
        private SerializerInterface $serializer,
        private GetBalanceService $service,
    ) {}

    #[Route('/balance/{id}', requirements: [
        'id' => Requirement::POSITIVE_INT,
    ])]
    public function __invoke(int $id): Response
    {
        $data = $this->serializer->serialize($this->service->getBalance($id), 'json');
        return JsonResponse::fromJsonString($data);
    }
}
