<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(private readonly UserRepository $userRepository, private readonly JWTTokenManagerInterface $jwtTokenManager)
    {
    }

    public function register(User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): User
    {
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt();

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    public function getUserFromToken(string $token): ?User
    {
        $token = str_replace('Bearer ', '', $token);

        $tokenData = $this->jwtTokenManager->parse($token);

        return $this->userRepository->getById($tokenData['username']);
    }
}