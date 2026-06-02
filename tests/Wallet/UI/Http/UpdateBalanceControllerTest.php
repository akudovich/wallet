<?php

declare(strict_types=1);

namespace App\Tests\Wallet\UI\Http;

use PHPUnit\Framework\Attributes\DataProvider;

final class UpdateBalanceControllerTest extends RestApiWebTestCase
{
    /**
     * @return list<array{int, array{amount: int, currency: string}, array{id: int, balance: int, currency: string}}>
     */
    public static function balanceProvider(): array
    {
        return [
            [
                1,
                [
                    'amount' => 100,
                    'currency' => 'RUB',
                ],
                [
                    'id' => 1,
                    'balance' => 100,
                    'currency' => 'RUB',
                ],
            ],
            [
                1,
                [
                    'amount' => 33,
                    'currency' => 'RUB',
                ],
                [
                    'id' => 1,
                    'balance' => 33,
                    'currency' => 'RUB',
                ],
            ],
        ];
    }

    /**
     * @param array{amount: int, currency: string} $data
     * @param array{id: int, balance: int, currency: string} $expected
     */
    #[DataProvider('balanceProvider')]
    public function testStock(int $id, array $data, array $expected): void
    {
        $this->client->request('POST', "/v1/balance/{$id}/debit/stock", $data);

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertIsBool($data);

        $this->checkBalance($id, $expected);
    }

    public function testRefund(): void
    {
        $this->client->request('POST', '/v1/balance/1/debit/stock', [
            'amount' => 100,
            'currency' => 'RUB',
        ]);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->client->request('POST', '/v1/balance/1/credit/refund', [
            'amount' => 70,
            'currency' => 'RUB',
        ]);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertIsBool($data);

        $this->checkBalance(1, [
            'id' => 1,
            'balance' => 30,
            'currency' => 'RUB',
        ]);
    }

    /**
     * @param array{id: int, balance: int, currency: string} $expected
     */
    public function checkBalance(int $id, array $expected): void
    {
        $this->client->request('GET', '/v1/balance/' . $id);

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertSame($data, $expected);
    }
}
