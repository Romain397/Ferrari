<?php

namespace App\Controller;

use App\Entity\SportAuto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SportAutoController extends AbstractController
{
    #[Route('/sport-auto', name: 'sport_auto')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $races = $doctrine->getRepository(SportAuto::class)->findAll();

        return $this->render('sport_auto/index.html.twig', [
            'races' => $races
        ]);
    }
}
