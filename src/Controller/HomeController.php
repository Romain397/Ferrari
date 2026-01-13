<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $cars = [
            'Ferrari 499P',
            'Ferrari 488 GTE',
            'Ferrari 458 Italia',
            'Ferrari FXX-K'
        ];

        return $this->render('home/home.html.twig', [
            'cars' => $cars
        ]);
    }

    #[Route('/sport-auto', name: 'sport_auto')]
    public function sportAuto(): Response
    {
        $palmares = [
            ['year' => 2023, 'title' => 'Victoire aux 24 Heures du Mans'],
            ['year' => 2024, 'title' => 'Championnat WEC Hypercar'],
            ['year' => 2025, 'title' => 'Double podium WEC']
        ];

        return $this->render('sport_auto/index.html.twig', [
            'palmares' => $palmares
        ]);
    }

    #[Route('/store', name: 'store')]
    public function store(): Response
    {
        return $this->render('store/index.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('contact/index.html.twig');
    }
}
