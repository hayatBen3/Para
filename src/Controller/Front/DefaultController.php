<?php

namespace App\Controller\Front;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProductRepository $productRepository): Response
    {

        $products = $productRepository->findAll();

        $productsHygiene = $productRepository->findBy(['category' => 1]);
        $productsSoins = $productRepository->findBy(['category' => 2]);
        $productsCosmetiques = $productRepository->findBy(['category' => 3]);
        $productsBebe = $productRepository->findBy(['category' => 4]);

        return $this->render('front/default/index.html.twig', [
            'products' => $products,
            'productsHygiene' => $productsHygiene,
            'productsSoins' => $productsSoins,
            'productsCosmetiques' => $productsCosmetiques,
            'productsBebe' => $productsBebe ,

          

        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager,)
    {
       
        $contact = new Contact();
        
        $firstName = $request->request->get('name');
        
      
        $email = $request->request->get('email');
        $message = $request->request->get('message');
        if ($request->isMethod('POST') ) {
    
         
         $contact->setFullname($firstName);
         $contact->setEmailadresse($email);
         $contact->setMessage($message);
        

         $entityManager->persist($contact);
         $entityManager->flush();
         $this->addFlash('success', 'Votre message a bien reçu.');
    }
        return $this->render('front/default/contact.html.twig', [
         
        ]);
    }


}
