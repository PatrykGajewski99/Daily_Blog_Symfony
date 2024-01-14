<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function register(User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): User
    {
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}