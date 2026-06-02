<?php

declare(strict_types=1);

namespace App\Tests\Wallet\UI\Http;

use Symfony\Component\HttpFoundation\Response;

final class GetBalanceControllerTest extends RestApiWebTestCase
{
    public function testNotExistReturnZeroBalance(): void
    {
        $this->client->request('GET', '/v1/balance/1');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertSame([
            'id' => 1,
            'balance' => 0,
            'currency' => 'RUB',
        ], $data);
    }

    public function testNotPositiveWalletIdExists(): void
    {
        $this->client->request('GET', '/v1/balance/0');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
