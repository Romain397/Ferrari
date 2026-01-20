<?php

namespace App\Form;

use App\Entity\CarArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'attr' => ['placeholder' => 'Ex: Ferrari SF90'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Titre de l’article'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['placeholder' => 'Description de la voiture (optionnelle)'],
            ])
            ->add('highlight', CheckboxType::class, [
                'label' => 'Mettre en avant ⭐',
                'required' => false,
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Année',
                'attr' => ['placeholder' => 'Ex: 2025'],
            ])
            ->add('image', TextType::class, [
                'label' => 'Image (URL ou chemin)',
                'required' => false,
                'attr' => ['placeholder' => '/images/499p.jpg'],
            ])
            ->add('video', UrlType::class, [
                'label' => 'Vidéo (URL)',
                'required' => false,
                'attr' => ['placeholder' => 'https://youtu.be/...'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarArticle::class,
            'attr' => ['novalidate' => 'novalidate'], // désactive la validation HTML5
        ]);
    }
}
