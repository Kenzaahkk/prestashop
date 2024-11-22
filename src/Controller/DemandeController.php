<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Form\DemandeType;
use App\Entity\Prestation;
use App\Repository\PrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/demande')]
final class DemandeController extends AbstractController
{
    #[Route(name: 'app_demande_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); 
        $demandes = $entityManager->getRepository(Demande::class)->findBy(['client' => $user]);

        return $this->render('demande/index.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/demandes-recues', name: 'demandes_recues', methods: ['GET'])]
    #[IsGranted('ROLE_PRESTATAIRE')]
    public function demandesRecues(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $prestations = $entityManager->getRepository(Prestation::class)->findBy(['prestataire' => $user]);
        $demandes = $entityManager->getRepository(Demande::class)->findBy(['prestation' => $prestations]);

        return $this->render('demande/demandes_recues.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/new/{id}', name: 'app_demande_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        Prestation $prestation,
        EntityManagerInterface $entityManager
    ): Response {
        $demande = new Demande();

        $form = $this->createForm(DemandeType::class, $demande, [
            'prestation' => $prestation, 
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setPrestation($prestation); 
            $demande->setClient($this->getUser()); 

            $entityManager->persist($demande);
            $entityManager->flush();

            $this->addFlash('success', 'Demande créée avec succès.');
            return $this->redirectToRoute('app_demande_index');
        }

        return $this->render('demande/new.html.twig', [
            'form' => $form->createView(),
            'prestation' => $prestation, 
        ]);
    }

    #[Route('/{id}', name: 'app_demande_show', methods: ['GET'])]
    public function show(Demande $demande): Response
    {
        return $this->render('demande/show.html.twig', [
            'demande' => $demande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_delete', methods: ['POST'])]
    public function delete(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $demande->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($demande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
    }
}
