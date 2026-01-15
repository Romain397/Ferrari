<?php

namespace App\Form;

use App\Entity\CarArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

class CarArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le modèle est obligatoire']),
                    // new Length([
                    //     'min' => 2,
                    //     'max' => 50,
                    //     'minMessage' => 'Le modèle doit contenir au moins {{ limit }} caractères',
                    //     'maxMessage' => 'Le modèle ne peut pas dépasser {{ limit }} caractères',
                    // ]),
                ],
                'attr' => ['placeholder' => 'Ex: Ferrari SF90'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire']),
                    // new Length([
                    //     'min' => 3,
                    //     'max' => 100,
                    //     'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                    //     'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères',
                    // ]),
                ],
                'attr' => ['placeholder' => 'Titre de l’article'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le contenu est obligatoire']),
                    // new Length([
                    //     'min' => 10,
                    //     'max' => 1000,
                    //     'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères',
                    //     'maxMessage' => 'Le contenu ne peut pas dépasser {{ limit }} caractères',
                    // ]),
                ],
                'attr' => ['placeholder' => 'Description de la voiture'],
            ])
            ->add('highlight', CheckboxType::class, [
                'label' => 'Mettre en avant ⭐',
                'required' => false,
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Année',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'année est obligatoire']),
                    new Positive(['message' => 'L\'année doit être positive']),
                    new Range([
                        'min' => 1900,
                        'max' => 2100,
                        'notInRangeMessage' => 'L\'année doit être comprise entre {{ min }} et {{ max }}',
                    ]),
                ],
                'attr' => ['placeholder' => 'Ex: 2025'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarArticle::class,
        ]);
    }
}
