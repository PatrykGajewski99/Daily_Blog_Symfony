<?php

namespace App\Controller;

use App\Form\UserRegisterType;
use App\ValueObject\CountryNames;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserController extends AbstractController
{
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
     *         @OA\Property(property="first_name", type="string", description="User's first name. Required."),
     *         @OA\Property(property="last_name", type="string", description="User's last name. Required."),
     *         @OA\Property(property="phone_number", type="string", description="User's phone number. Required. Must be between 9 and 13 characters."),
     *         @OA\Property(property="email", type="string", description="User's email address. Required. Must be a valid email."),
     *         @OA\Property(property="country", type="string", description="User's country. Required. Must be one of the allowed values: Poland, Germany, Spain, Portugal."),
     *         @OA\Property(property="town", type="string", description="User's town."),
     *         @OA\Property(
     *             property="password",
     *             type="object",
     *             description="User's password and password confirmation.",
     *             @OA\Property(property="first", type="string", description="User's password. Required. Must be at least 8 characters long."),
     *             @OA\Property(property="second", type="string", description="User's password confirmation. Required. Must match the password."),
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

            $user->setPassword($passwordHasher->hashPassword($user, $_ENV['PASSWORD_PLAINTEXT']));

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse($user, 201);
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors], 422);
    }
}
