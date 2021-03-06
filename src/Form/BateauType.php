<?php

namespace App\Form;

use App\Entity\Bateaux;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BateauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matricule')
            ->add('nom')
            ->add('metrage')
            ->add('owner' , EntityType::class , [
                'class' => \App\Entity\Client::class,
                'choice_label' => 'telephone'
            ])
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bateaux::class,
        ]);
    }
}
