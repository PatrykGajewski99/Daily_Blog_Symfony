<?php

namespace App\Hellpers;

use App\Entity\User;
use App\Factory\UserFactory;
use App\ValueObject\CountryNames;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Zenstruck\Foundry\Test\Factories;

class UserHelper
{
    use Factories;

    public static function createUser(Client $client, ?CountryNames $country = null): User
    {
        $user = UserFactory::createOne();

        $customer = $user->object();

        if ($country) {
            $customer->setCountry($country->value);
        }

        $client->post('register', ['json' => [
            'first_name'    => $customer->getFirstName(),
            'last_name'     => $customer->getLastName(),
            'phone_number'  => $customer->getPhoneNumber(),
            'email'         => $customer->getEmail(),
            'country'       => $customer->getCountry(),
            'town'          => $customer->getTown(),
            'password'      => [
                'first'     => $customer->getPassword(),
                'second'    => $customer->getPassword(),
            ]
        ]]);

        return $customer;
    }

    public static function loginUser(User $user, Client $client): string
    {
        $response = $client->post('login', ['json' => [
            'email'     => $user->getEmail(),
            'password'  => $user->getPassword()
        ]]);

        return json_decode($response->getBody()->getContents(), true)['token'];
    }
}