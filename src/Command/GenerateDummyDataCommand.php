<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Prestation;
use App\Entity\TypePrestation;
use Symfony\Component\Console\Attribute\AsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-dummy-data',
    description: 'Add a short description for your command',
)]
class GenerateDummyDataCommand extends Command
{
    protected static $defaultName = 'app:generate-dummy-data';
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Créer des types de prestation
        $type1 = new TypePrestation();
        $type1->setNom('Makeup');
        $this->entityManager->persist($type1);

        $type2 = new TypePrestation();
        $type2->setNom('Photographie');
        $this->entityManager->persist($type2);

        // Créer des utilisateurs prestataires
        $user1 = new User();
        $user1->setEmail('prestataire1@example.com')
            ->setPassword('password') // Attention : encode si nécessaire
            ->setRoles(['ROLE_PRESTATAIRE'])
            ->setNomSociete('BeautyPro');
        $this->entityManager->persist($user1);

        $user2 = new User();
        $user2->setEmail('prestataire2@example.com')
            ->setPassword('password')
            ->setRoles(['ROLE_PRESTATAIRE'])
            ->setNomSociete('PhotoArt');
        $this->entityManager->persist($user2);

        // Créer des prestations
        $prestation1 = new Prestation();
        $prestation1->setDateDebutDisponibilite(new \DateTime('2024-12-01'))
            ->setDateFinDisponibilite(new \DateTime('2024-12-10'))
            ->setPrix(500)
            ->setPrixJour(50)
            ->setPrestataire($user1)
            ->setTypePrestation($type1);
        $this->entityManager->persist($prestation1);

        $prestation2 = new Prestation();
        $prestation2->setDateDebutDisponibilite(new \DateTime('2024-11-20'))
            ->setDateFinDisponibilite(new \DateTime('2024-11-25'))
            ->setPrix(800)
            ->setPrixJour(100)
            ->setPrestataire($user2)
            ->setTypePrestation($type2);
        $this->entityManager->persist($prestation2);

        // Enregistrer tout dans la base
        $this->entityManager->flush();

        $output->writeln('Dummy data générée avec succès !');

        return Command::SUCCESS;
    }
}

// php bin/console app:generate-dummy-data  : taper cette commande pour exécuter le test
