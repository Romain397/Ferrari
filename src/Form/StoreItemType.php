<?php

namespace App\Form;

use App\Entity\StoreItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'T-shirt Ferrari']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 5, 'placeholder' => 'Description du produit...']
            ])
            ->add('type', TextType::class, [
                'label' => 'Type de produit',
                'attr' => ['placeholder' => 'Figurine, Casque, T-shirt...']
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix (€)',
                'currency' => 'EUR',
                'attr' => ['placeholder' => '49.99']
            ])
            ->add('image', TextType::class, [
                'label' => 'Image du produit',
                'required' => false,
                'attr' => ['placeholder' => '/images/tshirt_ferrari.jpg']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StoreItem::class,
        ]);
    }
}
