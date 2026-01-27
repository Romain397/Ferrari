<?php

namespace App\Form;

use App\Config\Category;
use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            // ───── Champs Post standard ─────
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Ferrari SF90'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Titre de l’article / course'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['placeholder' => 'Description (optionnelle)'],
            ])
            ->add('highlight', CheckboxType::class, [
                'label' => 'Mettre en avant ⭐',
                'required' => false,
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Année',
                'attr' => ['placeholder' => 'Ex: 2025', 'min' => 1930, 'max' => date("Y")],
            ])
            ->add('image', UrlType::class, [
                'label' => 'Image (URL ou chemin)',
                'required' => false,
                'attr' => ['placeholder' => '/images/499p.jpg'],
            ])
            ->add('video', UrlType::class, [
                'label' => 'Vidéo (URL)',
                'required' => false,
                'attr' => ['placeholder' => 'https://youtu.be/...'],
            ])
            ->add('category', EnumType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
            ])

            // ───── Champs spécifiques courses / SportAuto ─────
            ->add('circuitImage', UrlType::class, [
                'label' => 'Image du circuit',
                'required' => false,
                'attr' => ['placeholder' => '/images/monaco.jpg'],
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
