<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Payment;
use App\Entity\Purchase;
use App\Repository\OfferRepository;
use App\Repository\AccountRepository;


use Symfony\Component\HttpFoundation\Session\SessionInterface;



use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request,EntityManagerInterface $entityManager, SessionInterface $session , OfferRepository $offerRepository,AccountRepository $accountRepository): Response
    {
        $user = $this->getUser();
        $lastname = $user->getLastname();
        $firstname = $user->getFirstname();
        $email = $user->getEmail();
        $momo = $user->getDatanumber();
        $offerid= $session->get('offerid');

        $price = $session->get('price');
        $acountNumber = $session->get('accountNumber');


        if($request->isMethod('POST')){
            $id_transaction = $_POST['transaction-id'];
            $status_transaction = $_POST['transaction-status'];
              
          


            $paiement = new Payment();
            $paiement->setCreatedAt(new \DateTimeImmutable);
            $paiement->setAmount($price);
            $paiement->setStatut($status_transaction);
            $paiement->setIdTransaction($id_transaction);
            $paiement->setUser($user);

            $entityManager->persist($paiement);
            $entityManager->flush();

            if($status_transaction == "approved"){
                $purchase = new Purchase();

                $offer = $offerRepository->find(['id'=>$offerid]);
                //recuper id accound
                dd($acountNumber);
                $account = $accountRepository->accountbynumber($accountNumber);
                // $infoaccount = $accountRepository->findBy(['momoNumber'=>$acountNumber]);
                // dd($infoaccount);
                $account = $accountRepository->find(['momoNumber'=>$acountNumber]);
                $purchase->setCreatedAt(new \DateTimeImmutable);
                $purchase->setUser($user);
                $purchase->setAccount($account);
                $purchase->setStatut("En Attente");
                $purchase->setOffer($offer);


                $entityManager->persist($purchase);
                $entityManager->flush;

                $this->redirectToRoute('app_offer_index');

            }else{
                $this->redirectToRoute('app_home');
            }

        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'price' => $price,
            'user'=> [$lastname,$firstname,$email,$momo],
            'lastname'=>$lastname,
            'firstname'=>$firstname,
            'email'=>$email,
            'momo'=>$momo,
            'acountNumber'=>$acountNumber
        ]);
    }
}
