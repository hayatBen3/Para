<?php

namespace App\Controller\Front;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commandes')]
class OrderController extends AbstractController
{

    #[Route('/', name: 'my_orders')]
    public function myOrders(OrderRepository $orderRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('home');
        }
        $allMyOrders = $orderRepository->findByClient($user->getClient());

        $myOrders = $paginator->paginate(
            $allMyOrders,
            $request->query->getInt('page', 1), // Get the current page number from the request, default to 1
            8 // Items per page
        );



        return $this->render('front/order/my_orders.html.twig', [
            'myOrders' => $myOrders,
        ]);
    }

    #[Route('/ajout', name: 'add_order')]
    public function add(SessionInterface $session, Request $request, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {

        $panier = $session->get('panier', []);
        $user = $this->getUser();

        if (!$user) {

            return $this->redirectToRoute('app_login');
        }

        if ($panier === []) {
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('home');
        }

        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $phone = $request->request->get('phone');
        $address = $request->request->get('address');

        // for shipping 
        $shipping = $request->request->get('shipping');



        $order = new Order();

        // for show in create order
        $data = [];
        $initialTotal = 0;
        foreach ($panier as $id => $quantity) {
            $product = $productRepository->find($id);

            $data[] = [
                'product' => $product,
                'quantity' => $quantity
            ];

            $initialTotal += $product->getPrice() * $quantity;
        }




        if ($request->isMethod('POST') && $request->request->has('submit_button')) {

            // On remplit la commande
            $order->setClient($user->getClient());


            $order->setFirstName($firstName);
            $order->setLastName($lastName);
            $order->setPhone($phone);
            $order->setAddress($address);

            $totalFinal = 0;

            // On parcourt le panier pour créer les détails de commande
            foreach ($panier as $item => $quantity) {
                $orderDetails = new OrderDetails();

                // On va chercher le produit
                $product = $productRepository->find($item);

                $price = $product->getPrice();
                $product->setQuantity($product->getQuantity() - $quantity);

                // On crée le détail de commande
                $orderDetails->setProducts($product);
                $orderDetails->setPrice($price);
                $orderDetails->setQuantity($quantity);

                $order->addOrderDetail($orderDetails);


                $totalFinal += ($price * $quantity);
            }


            if ($shipping == 1) {

                $totalFinal = $totalFinal + 3;
            }
            $order->setTotal($totalFinal);
            // On persiste et on flush
            $em->persist($order);
            $em->flush();
            $reference = 'order-00' . $order->getId();
            $order->setReference($reference);
            $em->persist($order);
            $em->flush();


            $session->remove('panier');

            $this->addFlash('message', 'Commande créée avec succès');
            return $this->redirectToRoute('process_payment', ['id' => $order->getId()]);
        }

        return $this->render('front/order/new.html.twig', [
            'data' => $data,
            'initialTotal' => $initialTotal
        ]);
    }

    #[Route('/details/{id}', name: 'my_order_details')]
    public function myOrderDetail(Order $order, OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $clientConnected = $this->getUser()->getClient();
        if (!$user) {
            return $this->redirectToRoute('home');
        }
        /* check if order not in my orders */
        if ($clientConnected != $order->getClient()) {
            return $this->redirectToRoute('my_orders');
        }

        $myOrder = $orderRepository->find($order);

        return $this->render('front/order/details.html.twig', [
            'order' => $myOrder,
        ]);
    }




}
