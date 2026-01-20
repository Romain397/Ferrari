<?php

namespace App\Controller;

use App\Entity\CarArticle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(EntityManagerInterface $em): Response
    {
        // Récupérer tous les articles depuis la base
        $cars = $em->getRepository(CarArticle::class)->findBy(
            [],
            ['highlight' => 'DESC', 'year' => 'DESC'] // mettre à la une puis plus récent
        );

        return $this->render('home/home.html.twig', [
            'cars' => $cars
        ]);
    }

    #[Route('/sport-auto', name: 'sport_auto')]
    public function sportAuto(): Response
    {
        $palmares = [
            [
                'year' => 2023,
                'title' => 'Victoire aux 24 Heures du Mans',
                'major' => true
            ],
            [
                'year' => 2024,
                'title' => 'Champion du monde WEC Hypercar',
                'major' => true
            ],
            [
                'year' => 2025,
                'title' => 'Double podium en championnat WEC',
                'major' => false
            ],
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
