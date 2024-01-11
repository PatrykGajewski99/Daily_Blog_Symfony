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
     *         @OA\Property(property="first_name", type="string"),
     *         @OA\Property(property="last_name", type="string"),
     *         @OA\Property(property="phone_number", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="country", type="string"),
     *         @OA\Property(property="town", type="string")
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

            return new JsonResponse(['message' => 'Dane są prawidłowe'], 201);
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors], 422);
    }
}
