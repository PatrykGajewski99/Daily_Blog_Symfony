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

    public function testPostCreation(): void
    {
        $firstUserData = UserHelper::createData();
        $secondUserData = UserHelper::createData();

        $response = $this->httpClient->post('register', ['json' => [
            'first_name'    => $firstUserData->getFirstName(),
            'last_name'     => $firstUserData->getLastName(),
            'phone_number'  => $firstUserData->getPhoneNumber(),
            'email'         => $firstUserData->getEmail(),
            'country'       => $firstUserData->getCountry(),
            'town'          => $firstUserData->getTown(),
            'password'      => [
                'first'     => $firstUserData->getPassword(),
                'second'    => $firstUserData->getPassword(),
            ]
        ]]);

        $this->assertEquals(201, $response->getStatusCode());

        $firstUser = json_decode($response->getBody()->getContents(), true)['user'];

        $this->assertEquals($firstUserData->getEmail(), $firstUser['email']);
        $this->assertEquals($firstUserData->getPhoneNumber(), $firstUser['phone_number']);

        $response = $this->httpClient->post('register', ['json' => [
            'first_name'    => $secondUserData->getFirstName(),
            'last_name'     => $secondUserData->getLastName(),
            'phone_number'  => $secondUserData->getPhoneNumber(),
            'email'         => $secondUserData->getEmail(),
            'country'       => $secondUserData->getCountry(),
            'town'          => $secondUserData->getTown(),
            'password'      => [
                'first'     => $secondUserData->getPassword(),
                'second'    => $secondUserData->getPassword(),
            ]
        ]]);

        $this->assertEquals(201, $response->getStatusCode());

        $secondUser = json_decode($response->getBody()->getContents(), true)['user'];

        $this->assertEquals($secondUserData->getEmail(), $secondUser['email']);
        $this->assertEquals($secondUserData->getPhoneNumber(), $secondUser['phone_number']);

        $response = $this->httpClient->post('login', ['json' => [
            'email'     => $firstUser['email'],
            'password'  => $firstUserData->getPassword()
        ]]);

        $this->assertEquals(201, $response->getStatusCode());

        $firstUserLoginToken = json_decode($response->getBody()->getContents(), true)['token'];

        $response = $this->httpClient->post('login', ['json' => [
            'email'     => $secondUser['email'],
            'password'  => $secondUserData->getPassword()
        ]]);

        $this->assertEquals(201, $response->getStatusCode());

        $secondUserLoginToken = json_decode($response->getBody()->getContents(), true)['token'];

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
