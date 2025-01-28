<?php

namespace App\Controller;

use App\Entity\PaymentTermes;
use App\Form\PaymentTermesType;
use App\Repository\PaymentTermesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payment/termes')]
final class PaymentTermesController extends AbstractController
{
    #[Route(name: 'app_payment_termes_index', methods: ['GET'])]
    public function index(PaymentTermesRepository $paymentTermesRepository): Response
    {
        return $this->render('payment_termes/index.html.twig', [
            'payment_termes' => $paymentTermesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_payment_termes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paymentTerme = new PaymentTermes();
        $form = $this->createForm(PaymentTermesType::class, $paymentTerme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paymentTerme);
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_termes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payment_termes/new.html.twig', [
            'payment_terme' => $paymentTerme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_termes_show', methods: ['GET'])]
    public function show(PaymentTermes $paymentTerme): Response
    {
        return $this->render('payment_termes/show.html.twig', [
            'payment_terme' => $paymentTerme,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_payment_termes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PaymentTermes $paymentTerme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaymentTermesType::class, $paymentTerme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_termes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payment_termes/edit.html.twig', [
            'payment_terme' => $paymentTerme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_termes_delete', methods: ['POST'])]
    public function delete(Request $request, PaymentTermes $paymentTerme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentTerme->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($paymentTerme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payment_termes_index', [], Response::HTTP_SEE_OTHER);
    }
}
