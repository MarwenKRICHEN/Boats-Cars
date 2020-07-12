<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Moteur;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('num_series')
            ->add('marque')
            ->add('nBCheveaux')
            ->add('owner',EntityType::class,[
                'class'=> Client::class,
                'choice_label'=>'telephone'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Moteur::class,
        ]);
    }
}
