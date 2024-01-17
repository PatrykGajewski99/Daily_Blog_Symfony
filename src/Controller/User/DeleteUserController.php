<?php

namespace App\Controller\User;

use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DeleteUserController extends AbstractController
{
    public function __construct(private readonly UserService $userService, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Delete user account
     *
     * This call is used to delete user account
     *
     * @Route("/api/user/delete", name="delete_user_account", methods={"DELETE"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->userService->getUserFromToken($request->headers->get('Authorization'));

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse([], 200);
    }
}
