<?php

namespace App\Controller\Front;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'products_index')]
    public function index(ProductRepository $productRepository){
    
        $products = $productRepository->findAll();

        return $this->render('front/product/index.html.twig',[
            'products'=>$products
        ]);
    }

    #[Route('/{id}/detail', name: 'product_detail')]
    public function details(Product $product): Response
    {
        return $this->render('front/product/detail.html.twig', [
            'product'=>$product
        ]);
    }
}