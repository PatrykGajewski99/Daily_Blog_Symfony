<?php

namespace App\Tests;

use App\ValueObject\CountryNames;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class UserRegistrationTest extends TestCase
{
    private Client $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = new Client([
            'base_uri' => $_ENV['APP_API'],
        ]);
    }

    public function testUserRegistration(): void
    {
        $firstUserData = [
            'first_name'    => 'Patryk',
            'last_name'     => 'Gajewski',
            'phone_number'  => '777777777',
            'email'         => 'pgajewsk999@gmail.com',
            'country'       => CountryNames::PORTUGAL->value,
            'town'          => 'Skaryszew',
            'password'      => [
                'first'     => 'Qwerty123!',
                'second'    => 'Qwerty123!',
            ],
        ];

        $secondUserData = [
            'first_name'    => 'Patryk',
            'last_name'     => 'Gajewski',
            'phone_number'  => '777777777',
            'email'         => 'pgajewsk999@gmail.com',
            'country'       => CountryNames::PORTUGAL->value,
            'town'          => 'Skaryszew',
            'password'      => [
                'first'     => 'Qwerty123!',
                'second'    => 'Qwerty123!',
            ]
        ];

        $response = $this->httpClient->post('register', ['json' => $firstUserData]);

        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true)['user'];

        $this->assertEquals('pgajewsk999@gmail.com', $data['email']);
        $this->assertEquals('Skaryszew', $data['town']);
        $this->assertEquals('777777777', $data['phone_number']);
        $this->assertEquals('Gajewski', $data['last_name']);

        try {
            $this->httpClient->post('register', ['json' => $secondUserData]);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();

            $this->assertEquals(422, $response->getStatusCode());
        }
    }
}
