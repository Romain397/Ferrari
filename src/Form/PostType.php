<?php

namespace App\Form;

use App\Config\Category;
use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Ex. Ferrari 499P'],
            ])
            ->add('category', EnumType::class, [
                'label' => '📂 Catégorie (détermine le type)',
                'class' => Category::class,
                'choices' => [
                    'Voitures emblématiques (Homepage)' => Category::Voiture,
                    'Sport Auto / Courses' => Category::Course,
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description/Contenu',
                'required' => false,
                'attr' => ['rows' => 4, 'placeholder' => 'Décrivez le contenu de l\'article...'],
            ])

            // ───── CHAMPS POUR VOITURES (HOME) ─────
            ->add('model', TextType::class, [
                'label' => 'Modèle de voiture',
                'required' => false,
                'attr' => ['placeholder' => 'Ex. Ferrari SF90 Stradale'],
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Année',
                'required' => false,
                'attr' => ['placeholder' => 'Ex. 2025', 'min' => 1930, 'max' => date("Y")],
            ])
            ->add('image', TextType::class, [
                'label' => 'Image de la voiture (URL web)',
                'required' => false,
                'attr' => ['placeholder' => 'https://.../voiture.jpg'],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image de la voiture (fichier local)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*',
                ],
            ])
            ->add('video', UrlType::class, [
                'label' => 'Vidéo (YouTube/URL)',
                'required' => false,
                'attr' => ['placeholder' => 'https://youtube.com/...'],
            ])
            ->add('highlight', CheckboxType::class, [
                'label' => '⭐ Mettre en avant à la une',
                'required' => false,
            ])

            // ───── CHAMPS POUR SPORT AUTO (COURSES) ─────
            ->add('circuitImage', TextType::class, [
                'label' => 'Image du circuit (URL web)',
                'required' => false,
                'attr' => ['placeholder' => 'https://.../circuit.jpg'],
            ])
            ->add('circuitImageFile', FileType::class, [
                'label' => 'Image du circuit (fichier local)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*',
                ],
            ])
            ->add('raceDate', DateTimeType::class, [
                'label' => 'Date de la course',
                'widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'attr' => ['novalidate' => 'novalidate'], // désactive la validation HTML5
        ]);
    }
}
