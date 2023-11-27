<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=> false,
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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Mot de passe invalide',
                'options' => [
                    'attr' => ['class' => 'password']
                ],
                'required' => true,
                'first_options' => [
                    'label'=> false,
                    'attr' => ['placeholder' => 'Mot de passe']
                ],
                'second_options' => [
                    'label'=> false,
                    'attr' => ['placeholder' => 'Confirmation du mot de passe']
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Mot de passe invalide'
                    ])
                ]
            ])
            //->add('roles')
            //->add('created_at')
            //->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
