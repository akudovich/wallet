<?php

declare(strict_types=1);

namespace App\Tests\Wallet\UI\Http;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class RestApiWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $connection = $this->client->getContainer()
            ->get(Connection::class);

        $this->assertInstanceOf(Connection::class, $connection);
        $connection->executeStatement('TRUNCATE TABLE wallet CASCADE');
    }
}
