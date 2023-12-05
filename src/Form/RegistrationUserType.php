<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'lastname',
                    'placeholder' => 'Nom',
                    'required' => true
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Nom invalide'
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{L}\s-]+$/u',
                        'message' => 'Nom invalide'
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'firstname',
                    'placeholder' => 'Prénom',
                    'required' => true
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Prénom invalide'
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{L}\s-]+$/u',
                        'message' => 'Prénom invalide'
                    ])
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'phone',
                    'placeholder' => 'Téléphone',
                    'required' => true,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Numéro de téléphone invalide'
                    ]),
                    new Regex([
                        'pattern' => '/^0[1-9](\d{2}){4}$/',
                        'message' => 'Numéro de téléphone invalide'
                    ])
                ]
            ])
            ->add('company', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'company',
                    'placeholder' => 'Nom de l\'entreprise',
                    'required' => true
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Nom de l\'entreprise invalide'
                    ]),
                    new Regex([
                        'pattern' => '/^[^0-9$€£#\/\*\n\r\t\f\v]+$/u',
                        'message' => 'Nom de l\'entreprise invalide'
                    ])
                ]
            ])
            ->add('job', ChoiceType::class, [
                'label' => false,
                'placeholder' => 'Poste au sein de l\'entreprise',
                'attr' => [
                    'class' => 'job',
                    'required' => true
                ],
                'choices' => [
                    'Direction et Adminisatrion' => [
                        'Directeur Général' => 'Directeur Général',
                        'Directeur des Opérations' => 'Directeur des Opérations',
                        'Responsable Administratif et Financier' => 'Responsable Administratif et Financier'
                    ],
                    'Opérations et Logistique' => [
                        'Responsable Logistique' => 'Responsable Logistique',
                        'Chef d\'Exploitation' => 'Chef d\'Exploitation',
                        'Responsable Transport' => 'Responsable Transport',
                        'Planificateur Transport' => 'Planificateur Transport',
                        'Dispatcheur' => 'Dispatcheur'
                    ],
                    'Ressources Humaines et Formation' => [
                        'Responsable RH' => 'Responsable RH',
                        'Chargé de Recrutement' => 'Chargé de Recrutement',
                        'Formateur' => 'Formateur'
                    ],
                    'Commercial et Marketing' => [
                        'Responsable Commercial' => 'Responsable Commercial',
                        'Commercial' => 'Commercial',
                        'Assistant Commercial' => 'Assistant Commercial'
                    ],
                    'Support Administratif' => [
                        'Assistant Administratif' => 'Assistant Administratif',
                        'Assistant Comptable' => 'Assistant Comptable',
                        'Secrétaire' => 'Secrétaire'
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez choisir un poste'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
