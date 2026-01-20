<?php

namespace App\Form;

use App\Entity\SportAuto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SportAutoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom de la course',
                'attr' => ['placeholder' => 'Grand Prix de Monaco']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 5, 'placeholder' => 'Description de la course...']
            ])
            ->add('circuitImage', TextType::class, [
                'label' => 'Image du circuit',
                'required' => false,
                'attr' => ['placeholder' => '/images/monaco.jpg']
            ])
            ->add('video', TextType::class, [
                'label' => 'Vidéo / Replay',
                'required' => false,
                'attr' => ['placeholder' => 'https://youtu.be/...']
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date de la course',
                'widget' => 'single_text',
            ])
            ->add('category', TextType::class, [
                'label' => 'Catégorie',
                'attr' => ['placeholder' => 'Formule 1 ou WEC']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SportAuto::class,
        ]);
    }
}
