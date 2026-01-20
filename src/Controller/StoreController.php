<?php

namespace App\Controller;

use App\Entity\StoreItem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StoreController extends AbstractController
{
    #[Route('/store', name: 'store')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository(StoreItem::class)->findAll();

        return $this->render('store/index.html.twig', [
            'products' => $products
        ]);
    }
}
