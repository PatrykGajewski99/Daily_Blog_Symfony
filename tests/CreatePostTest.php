<?php

namespace App\Tests;

use App\Hellpers\UserHelper;
use App\ValueObject\CategoryNames;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;

class CreatePostTest extends TestCase
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
        $userData = UserHelper::createData();

        $response = $this->httpClient->post('register', ['json' => [
            'first_name'    => $userData->getFirstName(),
            'last_name'     => $userData->getLastName(),
            'phone_number'  => $userData->getPhoneNumber(),
            'email'         => $userData->getEmail(),
            'country'       => $userData->getCountry(),
            'town'          => $userData->getTown(),
            'password'      => [
                'first'     => $userData->getPassword(),
                'second'    => $userData->getPassword(),
            ]
        ]]);

        $this->assertEquals(201, $response->getStatusCode());

        $user = json_decode($response->getBody()->getContents(), true)['user'];

        $response = $this->httpClient->post('login', ['json' => [
            'email'     => $user['email'],
            'password'  => $userData->getPassword()
        ]]);

        $this->assertEquals(201, $response->getStatusCode());

        $token = json_decode($response->getBody()->getContents(), true)['token'];

        $response = $this->httpClient->post('post/create', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
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
    }
}
