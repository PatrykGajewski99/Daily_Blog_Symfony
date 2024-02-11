<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Service\PostService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class EditPostController extends AbstractController
{
    public function __construct(private readonly UserService $userService, private readonly PostService $postService)
    {

    }

    /**
     * Edit post
     *
     * This call is used to edit post
     *
     * @Route("/api/post/{post}/edit", name="edit_post", methods={"PUT"})
     *
     * @OA\RequestBody(
     *     description="Post data",
     *     required=false,
     *     @OA\JsonContent(
     *         @OA\Property(property="category", type="string", description="Post category", example="Sport"),
     *         @OA\Property(property="title", type="string", description="Post title"),
     *         @OA\Property(property="description", type="string", description="Post description"),
     *     )
     * )
     *
     * @param Post $post
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Post $post, Request $request): JsonResponse
    {
        $user = $this->userService->getUserFromToken($request->headers->get('Authorization'));

        if ($post->getUser()->getUserIdentifier() !== $user->getUserIdentifier()) {
            return new JsonResponse([
                'error' => 'You can not edit other customers posts.',
            ], 422);
        }

       return $this->postService->update($post, $request->getContent());
    }
}
