<?php

namespace App\Form;

use App\Entity\Product;
use App\Config\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Ex: T-shirt Ferrari']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 5, 'placeholder' => 'Description du produit...']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de produit',
                'choices' => [
                    'Merchandising' => Type::MERCH,
                    'Accessoire' => Type::ACCESSOIRE,
                    'Vêtement' => Type::VETEMENT,
                ],
                'expanded' => false, // select dropdown
                'multiple' => false,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix (€)',
                'currency' => 'EUR',
                'attr' => ['placeholder' => '49.99']
            ])
            ->add('image', TextType::class, [
                'label' => 'Image du produit (URL)',
                'required' => false,
                'attr' => ['placeholder' => '/images/tshirt_ferrari.jpg']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
