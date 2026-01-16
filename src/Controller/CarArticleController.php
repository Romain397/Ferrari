<?php

namespace App\Controller;

use App\Entity\CarArticle;
use App\Form\CarArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CarArticleController extends AbstractController
{
    #[Route('/admin/car-article/new', name: 'car_article_new')]
    public function new(Request $request): Response
    {
        // 1️⃣ Instancier l'entité
        $carArticle = new CarArticle();

        // 2️⃣ Créer le formulaire à partir du Type
        $form = $this->createForm(CarArticleType::class, $carArticle);

        // 3️⃣ Ecouter la requête
        $form->handleRequest($request);

        // 4️⃣ Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Debug : vérifier que l'objet est bien hydraté
            dump($carArticle); // Symfony Profiler affichera toutes les données

            // Message de confirmation
            $this->addFlash('success', 'L’article a été validé !');

        }

        // 5️⃣ Rendu du template
        return $this->render('admin/car_article_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
