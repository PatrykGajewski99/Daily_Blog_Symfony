<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListPostsController extends AbstractController
{
    public function __construct(private readonly UserService $userService, private readonly PostRepository $postRepository)
    {
    }

    /**
     * Get posts
     *
     * This call is used to get all own posts
     *
     * @Route("/api/posts", name="get_posts", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->userService->getUserFromToken($request->headers->get('Authorization'));

        $userPosts = $this->postRepository->getUserPosts($user);

        $serializedPosts = array_map(fn (Post $post) => $post->serialize(), $userPosts);

        return new JsonResponse(['posts' => $serializedPosts]);
    }
}
