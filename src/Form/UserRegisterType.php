<?php

namespace App\Form;

use App\Entity\User;
use App\ValueObject\CountryNames;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("first_name", TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'First name field is required',
                    )
                ],
            ])
            ->add("last_name", TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Last name field is required'
                    )
                ],
            ])
            ->add("email", EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Email field is required'
                    ),
                    new Assert\Email()
                ],
            ])
            ->add("phone_number", TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Phone number field is required'
                    ),
                    new Assert\Length(
                        min: 9,
                        max: 13,
                        minMessage: 'The phone number must be at least 9 characters long',
                        maxMessage: 'The phone number cannot be longer than 13 characters',
                    )
                ],
            ])
            ->add("country", TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Country field is required'
                    ),
                    new Assert\Choice(callback: [CountryNames::class , 'getAllowedValues'])
                ],
            ])
            ->add("town", TextType::class, [])
            ->add("password", PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Town field is required'
                    ),
                    new Assert\Length(
                        min: 8,
                        minMessage: 'The password must be at least 8 characters long',
                    )
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
