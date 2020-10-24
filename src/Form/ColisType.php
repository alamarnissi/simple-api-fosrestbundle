<?php

namespace App\Form;

use App\Entity\Colis;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', Utilisateur::class)
            ->add('products', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Colis::class,
            'csrf_protection'=> false
        ]);
    }
}
