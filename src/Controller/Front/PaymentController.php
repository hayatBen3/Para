<?php

namespace App\Controller\Front;

use DateTime;
use DateTimeZone;
use Stripe\Stripe;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/payment')]
class PaymentController extends AbstractController
{



    #[Route('/order/{id}', name: 'process_payment')]
    public function processPayment(Order $order): Response
    {
        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];

        $stripe = new \Stripe\StripeClient($stripeSecretKey);

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $order->getReference(),
                        ],
                        'unit_amount' => $order->getTotal() * 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl("cancel_url", [], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);

        return $this->redirect($checkout_session->url, 303);

    }

    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelPayment(): Response
    {



        // return $this->render('home/payment/payment-failed.html.twig', []);
    }


    #[Route('/success-url/{id}', name: 'success_url')]
    public function successPayment(Order $order, EntityManagerInterface $em): Response
    {

        $order->setIsPaidByCard(true);

        $currentDateTime = new DateTime();
        $currentDateTime->setTimezone(new DateTimeZone('Africa/Casablanca'));
        $order->setDatePaiement($currentDateTime);

        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute('my_orders');
    }

}


