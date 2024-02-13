<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Service\PostService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CreatePostController extends AbstractController
{
    public function __construct(private readonly UserService $userService, private readonly PostService $postService)
    {

    }

    /**
     * Create post
     *
     * This call is used to create a new blog post
     *
     * @Route("/api/post/create", name="post_creation", methods={"POST"})
     *
     * @OA\RequestBody(
     *     description="Post data",
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="category", type="string", description="Post category", example="Sport"),
     *         @OA\Property(property="title", type="string", description="Post title"),
     *         @OA\Property(property="description", type="string", description="Post description"),
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $request->headers->get('Authorization');

        if (!$token) {
            return new JsonResponse(['errors' => 'Can not create post like not logged user'], 422);
        }

        $user = $this->userService->getUserFromToken($token);

        $post = $this->postService->create($user, $data);

        return $post instanceof Post
            ? new JsonResponse(['post'   => $post->serialize()], 201)
            : new JsonResponse(['errors' => $post], 422);
    }
}