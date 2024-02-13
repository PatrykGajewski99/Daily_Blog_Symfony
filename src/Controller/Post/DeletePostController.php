<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DeletePostController extends AbstractController
{
    public function __construct(private readonly UserService $userService, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Delete post
     *
     * This call is used to delete post
     *
     * @Route("/api/post/{post}/delete", name="delete_post", methods={"DELETE"})
     *
     * @param Post $post
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Post $post, Request $request): JsonResponse
    {
        $user = $this->userService->getUserFromToken($request->headers->get('Authorization'));

        if ($post->getUser()->getUserIdentifier() !== $user->getUserIdentifier()) {
            return new JsonResponse(['error' => 'You can not delete other customers posts.']);
        }

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return new JsonResponse([], 204);
    }
}
