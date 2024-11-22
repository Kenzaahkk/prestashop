<?php

namespace App\Form;

use App\Entity\Prestation;
use App\Entity\TypePrestation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebutDisponibilite', null, [
                'widget' => 'single_text',
            ])
            ->add('dateFinDisponibilite', null, [
                'widget' => 'single_text',
            ])
            ->add('prix')
            ->add('prixJour')
            ->add('typePrestation', EntityType::class, [
                'class' => typePrestation::class, 
                'choice_label' => 'nom', 
                'placeholder' => 'Choisissez un type de prestation', 
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestation::class,
        ]);
    }
}
