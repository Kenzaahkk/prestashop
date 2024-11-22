<?php

namespace App\Controller;

use App\Entity\Prestation;
use App\Entity\Commentaire;
use App\Entity\Demande;
use App\Form\CommentaireType;
use App\Repository\DemandeRepository;
use App\Form\PrestationType;
use App\Form\SearchPrestationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/prestation')]
final class PrestationController extends AbstractController
{

    #[Route('/prestations/{id}', name: 'app_prestation_details', methods: ['GET'])]
    public function details(Prestation $prestation, DemandeRepository $demandeRepository): Response
    {
        $demandes = $demandeRepository->findByPrestation($prestation);

        return $this->render('prestation/details.html.twig', [
            'prestation' => $prestation,
            'demandes' => $demandes,
        ]);
    }

    #[IsGranted('ROLE_PRESTATAIRE')]
    #[Route(name: 'app_prestation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $prestations = $entityManager->getRepository(Prestation::class)->findBy(['prestataire' => $user]);

        return $this->render('prestation/index.html.twig', [
            'prestations' => $prestations,
        ]);
    }

    #[Route('/prestations', name: 'app_prestations_public', methods: ['GET', 'POST'])]
    public function publicIndex(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SearchPrestationType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $queryBuilder = $entityManager->getRepository(Prestation::class)->createQueryBuilder('p');

        if ($form->isSubmitted() && $form->isValid()) {
            $criteria = $form->getData();

            if (!empty($criteria['type'])) {
                $queryBuilder->andWhere('p.typePrestation = :type')
                    ->setParameter('type', $criteria['type']);
            }

            if (!empty($criteria['prixMax'])) {
                $queryBuilder->andWhere('p.prix <= :prixMax')
                    ->setParameter('prixMax', $criteria['prixMax']);
            }

            if (!empty($criteria['dateDebut'])) {
                $queryBuilder->andWhere('p.dateDebutDisponibilite >= :dateDebut')
                    ->setParameter('dateDebut', $criteria['dateDebut']);
            }

            if (!empty($criteria['dateFin'])) {
                $queryBuilder->andWhere('p.dateFinDisponibilite <= :dateFin')
                    ->setParameter('dateFin', $criteria['dateFin']);
            }
        }

        $prestations = $queryBuilder->getQuery()->getResult();

        return $this->render('prestation/public_index.html.twig', [
            'form' => $form->createView(),
            'prestations' => $prestations,
        ]);
    }

    #[IsGranted('ROLE_PRESTATAIRE')]
    #[Route('/new', name: 'app_prestation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prestation = new Prestation();
        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $prestation->setPrestataire($user);

            $entityManager->persist($prestation);
            $entityManager->flush();

            return $this->redirectToRoute('app_prestation_index');
        }

        return $this->render('prestation/new.html.twig', [
            'prestation' => $prestation,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_PRESTATAIRE')]
    #[Route('/{id}', name: 'app_prestation_show', methods: ['GET'])]
    public function show(Prestation $prestation): Response
    {
        return $this->render('prestation/show.html.twig', [
            'prestation' => $prestation,
        ]);
    }

    #[IsGranted('ROLE_PRESTATAIRE')]
    #[Route('/{id}/edit', name: 'app_prestation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prestation $prestation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prestation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prestation/edit.html.twig', [
            'prestation' => $prestation,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_PRESTATAIRE')]
    #[Route('/{id}', name: 'app_prestation_delete', methods: ['POST'])]
    public function delete(Request $request, Prestation $prestation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $prestation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($prestation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prestation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/prestation/{id}/comment', name: 'prestation_comment', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CLIENT')]
    public function comment(
        Request $request,
        Prestation $prestation,
        EntityManagerInterface $entityManager
    ): Response {
        $client = $this->getUser();
        $demande = $entityManager->getRepository(Demande::class)->findOneBy([
            'client' => $client,
            'prestation' => $prestation,
        ]);

        if (!$demande) {
            $this->addFlash('error', 'Vous ne pouvez commenter que les prestations pour lesquelles vous avez fait une demande.');
            return $this->redirectToRoute('app_prestations_public');
        }

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setClient($client);
            $commentaire->setPrestation($prestation);

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté.');
            return $this->redirectToRoute('app_prestation_details', ['id' => $prestation->getId()]);
        }

        return $this->render('commentaire/new.html.twig', [
            'form' => $form,
            'prestation' => $prestation,
        ]);
    }
}
