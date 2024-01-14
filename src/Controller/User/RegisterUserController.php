<?php

namespace App\Controller\User;

use App\Form\UserRegisterType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;

class RegisterUserController extends AbstractController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * Register user
     *
     * This call is used to create a new user
     *
     * @Route("/api/register", name="registration", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="User data",
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="first_name", type="string", description="User's first name. Required.", example="Patryk"),
     *         @OA\Property(property="last_name", type="string", description="User's last name. Required.", example="Gajewski"),
     *         @OA\Property(property="phone_number", type="string", description="User's phone number. Required. Must be between 9 and 13 characters.", example="777878727"),
     *         @OA\Property(property="email", type="string", description="User's email address. Required. Must be a valid email.", example="example22@gmail.com"),
     *         @OA\Property(property="country", type="string", description="User's country. Required. Must be one of the allowed values.", example="Poland"),
     *         @OA\Property(property="town", type="string", example="Warsaw"),
     *         @OA\Property(
     *             property="password",
     *             type="object",
     *             description="User's password and password confirmation.",
     *             @OA\Property(property="first", type="string", description="User's password. Required. Must be at least 8 characters long.", example="YourPassword123"),
     *             @OA\Property(property="second", type="string", description="User's password confirmation. Required. Must match the password.", example="YourPassword123"),
     *         ),
     *     )
     * )
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->register($user, $entityManager, $passwordHasher);

            return new JsonResponse(['user' => $user->serialize()], 201);
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors], 422);
    }
}
