<?php

declare(strict_types=1);

namespace App\Tests\Wallet\UI\Http;

use App\Wallet\Domain\Currency;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;

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
        $response = $this->debit($id, $data['amount'], $data['currency']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertIsBool($data);
        $this->assertTrue($data);

        $this->checkBalance($id, $expected);
    }

    public function testRefund(): void
    {
        $response = $this->debit(1, 100, 'RUB');
        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->credit(1, 70, 'RUB');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertIsBool($data);
        $this->assertTrue($data);

        $this->checkBalance(1, [
            'id' => 1,
            'balance' => 30,
            'currency' => 'RUB',
        ]);
    }

    public function testCurrencyMismatch(): void
    {
        $response = $this->debit(1, 100, 'USD');
        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->debit(1, 1, 'RUB');
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertIsString($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
        $this->assertIsString($data['error']);
        $this->assertStringStartsWith('Currency mismatch', $data['error']);

        $response = $this->credit(1, 1, 'RUB');
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertIsString($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
        $this->assertIsString($data['error']);
        $this->assertStringStartsWith('Currency mismatch', $data['error']);
    }

    public function testFullRefund(): void
    {
        $this->debit(1, 100, 'RUB');
        $this->credit(1, 100, 'RUB');
        $this->checkBalance(1, [
            'id' => 1,
            'balance' => 0,
            'currency' => 'RUB',
        ]);
    }

    public function testInsufficientFunds(): void
    {
        $this->debit(1, 100, 'RUB');
        $this->credit(1, 101, 'RUB');
        $this->assertEquals(409, $this->client->getResponse()->getStatusCode());
        $this->assertIsString($this->client->getResponse()->getContent());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
        $this->assertIsString($data['error']);
        $this->assertStringStartsWith('Insufficient funds', $data['error']);
    }

    /**
     * @param array{id: int, balance: int, currency: string} $expected
     */
    private function checkBalance(int $id, array $expected): void
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

    private function debit(int $id, int $amount, string $currency): Response
    {
        $this->client->request('POST', "/v1/balance/{$id}/debit/stock", [
            'amount' => $amount,
            'currency' => $currency,
        ]);
        return $this->client->getResponse();
    }

    private function credit(int $id, int $amount, string $currency): Response
    {
        $this->client->request('POST', "/v1/balance/{$id}/credit/refund", [
            'amount' => $amount,
            'currency' => $currency,
        ]);
        return $this->client->getResponse();
    }
}
