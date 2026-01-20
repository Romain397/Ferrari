<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_index')]
    public function index(): Response
    {
        // Ici le panier peut venir du localStorage ou de la BDD pour un user connecté
        return $this->render('cart/cart.html.twig');
    }
}
