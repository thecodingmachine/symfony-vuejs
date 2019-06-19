<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use Safe\Exceptions\JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Safe\json_decode;
use function Safe\json_encode;

abstract class AbstractControllerWebTestCase extends WebTestCase
{
    /** @var KernelBrowser */
    protected $client;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->client = static::createClient();
    }

    /**
     * @param mixed[] $data
     *
     * @throws JsonException
     */
    protected function JSONRequest(string $method, string $uri, array $data = []): void
    {
        $this->client->request($method, $uri, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
    }

    /**
     * @return mixed
     *
     * @throws JsonException
     */
    protected function assertJSONResponse(Response $response, int $expectedStatusCode)
    {
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());

        return json_decode($response->getContent(), true);
    }

    /**
     * @throws JsonException
     */
    protected function login(string $username = UserFixtures::DEFAULT_USER_LOGIN, string $password = UserFixtures::DEFAULT_USER_PASSWORD): void
    {
        $this->client->request(Request::METHOD_POST, '/api/security/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['username' => $username, 'password' => $password]));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
