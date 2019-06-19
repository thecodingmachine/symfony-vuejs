<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use Safe\Exceptions\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function count;

final class PostControllerTest extends AbstractControllerWebTestCase
{
    /**
     * @throws JsonException
     */
    public function testCreatePost(): void
    {
        // test that sending a request without being authenticated will result to a unauthorized HTTP code.
        $this->JSONRequest(Request::METHOD_POST, '/api/posts');
        $this->assertJSONResponse($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
        // test that sending a request while not having the role "ROLE_FOO" will result to a forbidden HTTP code.
        $this->login(UserFixtures::USER_LOGIN_ROLE_BAR, UserFixtures::USER_PASSWORD_ROLE_BAR);
        $this->JSONRequest(Request::METHOD_POST, '/api/posts');
        $this->assertJSONResponse($this->client->getResponse(), Response::HTTP_FORBIDDEN);
        // test that sending a request while begin authenticated will result to a created HTTP code.
        $this->login();
        $this->JSONRequest(Request::METHOD_POST, '/api/posts', ['message' => 'Hello world!']);
        $this->assertJSONResponse($this->client->getResponse(), Response::HTTP_CREATED);
        // test that sending no message will result to a bad request HTTP code.
        $this->JSONRequest(Request::METHOD_POST, '/api/posts');
        $this->assertJSONResponse($this->client->getResponse(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws JsonException
     */
    public function testFindAllPosts(): void
    {
        // test that sending a request without being authenticated will result to a unauthorized HTTP code.
        $this->client->request(Request::METHOD_GET, '/api/posts');
        $this->assertJSONResponse($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
        // test that sending a request while begin authenticated will result to a OK HTTP code.
        $this->login();
        $this->client->request(Request::METHOD_GET, '/api/posts');
        $response = $this->client->getResponse();
        $content = $this->assertJSONResponse($response, Response::HTTP_OK);
        $this->assertEquals(1, count($content));
    }
}
