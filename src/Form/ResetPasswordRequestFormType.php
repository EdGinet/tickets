<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'email',
                    'placeholder' => 'Email',
                    'required' => true
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Email invalide'
                    ]),
                    new Email([
                        'message' => 'Format d\'email invalide'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 180,
                        'minMessage' => 'L\'email doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'L\'email doit contenir au maximum {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'submit-btn'
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
