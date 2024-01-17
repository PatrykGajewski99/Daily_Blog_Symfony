<?php

namespace App\Form;

use App\Entity\Post;
use App\ValueObject\CategoryNames;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EditPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("category", TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Choice(
                        callback: [CategoryNames::class , 'getAllowedValues'],
                        message: 'This category name is not allowed.'
                    )
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class' => Post::class,
        ]);
    }
}
