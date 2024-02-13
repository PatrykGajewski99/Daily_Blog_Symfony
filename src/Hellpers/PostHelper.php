<?php

namespace App\Hellpers;

use App\ValueObject\CategoryNames;
use GuzzleHttp\Client;
use Zenstruck\Foundry\Test\Factories;

class PostHelper
{
    use Factories;

    public static function create(Client $client, string $token, string $category, string $title, string $description): array
    {
        $response = $client->post('post/create', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'category'      => $category,
                'title'         => $title,
                'description'   => $description,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true)['post'];
    }
}