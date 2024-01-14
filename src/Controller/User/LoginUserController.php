<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class LoginUserController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * Login user
     *
     * This call is used to login user into system
     *
     * @Route("/api/login", name="login", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="User data",
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="email", type="string", description="User's email address. Required. Must be a valid email.", example="example22@gmail.com"),
     *         @OA\Property(property="password", type="string", description="User's password.", example="Qwerty123#"),
     *     )
     * )
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->userRepository->getByEmail($data['email']);

        if (is_null($user)) {
            return new JsonResponse(['message' => 'User with this email does not exist'], 404);
        }

        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['message' => 'Invalid password'], 422);
        }

        return new JsonResponse([], 204);
    }
}
