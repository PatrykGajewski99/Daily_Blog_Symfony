<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetPostController extends AbstractController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * Get post
     *
     * This call is used to get post
     *
     * @Route("/api/post/{post}", name="get_post", methods={"GET"})
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

        return new JsonResponse(['post' => $post->serialize()], 200);
    }
}
