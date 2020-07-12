<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ref')
            ->add('nom')
            ->add('neuf', ChoiceType::class, [
                'choices' =>[
                    'Neuf'=> true,
                    'Occasion' => false
                ],

                'multiple' => false,
                'expanded'=>true
            ])

            ->add('cat' , ChoiceType::class, [
                'choices'=>[
                    'Accessoires marin' => 'Accessoires marin',
                    'Alternateurs' => 'Alternateurs',
                    'Boite de vitesse marine' =>   'Boite de vitesse marine',
                    'Commande et direction'=> 'Commande et direction'


                ]
            ])
            ->add('prix')
            ->add('image1' , FileType::class, [

                'mapped'=> false,
                'multiple'=> false,
                'required'=>false

            ])
            ->add('image2' , FileType::class, [

                'mapped'=> false,
                'multiple'=> false,
                'required'=>false

            ])
            ->add('image3' , FileType::class, [

                'mapped'=> false,
                'multiple'=> false,
                'required'=>false

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
