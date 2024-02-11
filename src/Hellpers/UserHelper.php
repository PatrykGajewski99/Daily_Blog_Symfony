<?php

namespace App\Hellpers;

use App\Entity\User;
use App\Factory\UserFactory;
use App\ValueObject\CountryNames;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\Factories;

class UserHelper
{
    use Factories;

    public static function createData(?CountryNames $country = null): User
    {
        $user = UserFactory::createOne();

        $customer = $user->object();

        if ($country) {
            $customer->setCountry($country->value);
        }

        return $customer;
    }
}