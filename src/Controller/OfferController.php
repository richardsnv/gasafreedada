<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Form\OfferType;
use App\Repository\AccountRepository;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offer')]
final class OfferController extends AbstractController
{
    #[Route(name: 'app_offer_index', methods: ['GET', 'POST'])]
    public function index(OfferRepository $offerRepository, AccountRepository $accountRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $offer = new Offer();
        $accounts = $accountRepository->findBy(['user' => $user]);
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);
        // dd($accounts);
        if ($form->isSubmitted() && $form->isValid()) {
            $offer->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($offer);

            $entityManager->flush();

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offer/index.html.twig', [
            'offers' => $offerRepository->findAll(),
            'user' => $user,
            'accounts' => $accounts, ]);
    }

    #[Route('/new', name: 'app_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $offer = new Offer();
        // $accounts = $accountRepository->findBy(['user' => $user]);
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($offer);

            $entityManager->flush();

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offer/new.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_show', methods: ['GET'])]
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_delete', methods: ['POST'])]
    public function delete(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
    }
}
