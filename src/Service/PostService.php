<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Form\CreatePostType;
use App\Form\EditPostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class PostService
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly FormFactoryInterface $formFactory, private readonly SerializerInterface $serializer)
    {
    }

    public function create(User $user, array $data): Post|array
    {
        $post = new Post();

        $form = $this->formFactory->create(CreatePostType::class, $post);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($user);
            $post->setCreatedAt();

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return $post;
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }

    public function update(Post $post, mixed $data): JsonResponse
    {
        $form = $this->formFactory->create(EditPostType::class, $post);
        $form->submit(json_decode($data, true));

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->serializer->deserialize($data, Post::class, 'json', ['object_to_populate' => $post,]);

                $post->setUpdatedAt();

                $this->entityManager->flush();

                return new JsonResponse(['message' => $post->serialize()], 200);
            } catch (NotEncodableValueException $e) {
                return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['error' => $errors], 404);
    }
}