<?php

namespace App\Form;

use App\Entity\Demande;
use App\Entity\Prestation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;


class DemandeType extends AbstractType
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebutPrestation', null, [
                'widget' => 'single_text',
                'label' => 'Date de début',
            ])
            ->add('dateFinPrestation', null, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
            ]);
    
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            $data = $event->getData();
            $form = $event->getForm();
    
            $prestation = $options['prestation'];
            if (!$prestation) {
                $form->addError(new FormError('Prestation introuvable.'));
                return;
            }

            $dateDebutPrestation = new \DateTime($data['dateDebutPrestation']);
            $dateFinPrestation = new \DateTime($data['dateFinPrestation']);

            if ($dateDebutPrestation < $prestation->getDateDebutDisponibilite() ||
                $dateFinPrestation > $prestation->getDateFinDisponibilite()) {
                $form->addError(new FormError(sprintf(
                    'Les dates doivent être comprises entre le %s et le %s.',
                    $prestation->getDateDebutDisponibilite()->format('d/m/Y'),
                    $prestation->getDateFinDisponibilite()->format('d/m/Y')
                )));
            }
    
            $overlappingDemands = $this->entityManager->getRepository(Demande::class)
                ->findOverlappingDemands(
                    new \DateTime($data['dateDebutPrestation']),
                    new \DateTime($data['dateFinPrestation']),
                    $prestation
                );
    
            if (!empty($overlappingDemands)) {
                $form->addError(new FormError('Les dates sélectionnées ne sont pas disponibles.'));
            }
        });
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
            'prestation' => null,
        ]);
    }
}
