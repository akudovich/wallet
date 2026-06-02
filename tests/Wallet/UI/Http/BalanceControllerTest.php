<?php

declare(strict_types=1);

namespace App\Tests\Wallet\UI\Http;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class BalanceControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $connection = $this->client->getContainer()
            ->get(Connection::class);
        $this->assertInstanceOf(Connection::class, $connection);
        $connection->executeStatement('DELETE FROM wallet');
    }

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
