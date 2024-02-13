<?php

namespace App\Tests;

use App\Hellpers\UserHelper;
use App\ValueObject\CategoryNames;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;

class EditPostCaseTest extends TestCase
{
    use Factories;

    private Client $httpClient;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->httpClient = new Client([
            'base_uri' => $_ENV['APP_API'],
            'defaults' => [
                'exceptions' => false
            ]
        ]);
    }

    public function testEditPost(): void
    {
        $firstUser = UserHelper::createUser($this->httpClient);
        $secondUser = UserHelper::createUser($this->httpClient);

        $firstUserLoginToken = UserHelper::loginUser($firstUser, $this->httpClient);
        $secondUserLoginToken = UserHelper::loginUser($secondUser, $this->httpClient);

        $response = $this->httpClient->post('post/create', [
            'headers' => [
                'Authorization' => 'Bearer ' . $firstUserLoginToken,
            ],
            'json' => [
                'category'      => CategoryNames::HEALTH->value,
                'title'         => 'Sport is the best to keep healthy condition',
                'description'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce elementum quam sed pharetra feugiat. Fusce sit amet euismod augue, sit amet sagittis metus. Suspendisse potenti. Vivamus tincidunt nulla vitae massa porta vulputate. Nulla lacinia ligula non dui dictum, nec consequat ligula molestie.',
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true)['post'];

        $this->assertEquals('Sport is the best to keep healthy condition', $data['title']);
        $this->assertEquals(CategoryNames::HEALTH->value, $data['category']);

        $response = $this->httpClient->put('post/' . $data['id'] . '/edit', [
            'headers' => [
                'Authorization' => 'Bearer ' . $firstUserLoginToken,
            ],
            'json' => [
                'category' => CategoryNames::SPORT->value,
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true)['post'];

        $this->assertEquals('Sport is the best to keep healthy condition', $data['title']);
        $this->assertEquals(CategoryNames::SPORT->value, $data['category']);

        try {
            $this->httpClient->put('post/' . $data['id'] . '/edit', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secondUserLoginToken,
                ],
                'json' => [
                    'category' => CategoryNames::HEALTH->value,
                ]
            ]);
        }catch (ClientException $exception) {
            $response = $exception->getResponse();

            $this->assertEquals(422, $response->getStatusCode());
        }
    }
}
