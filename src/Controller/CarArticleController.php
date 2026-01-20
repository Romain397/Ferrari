<?php

namespace App\Controller;

use App\Entity\CarArticle;
use App\Form\CarArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CarArticleController extends AbstractController
{
    #[Route('/admin/car-article/new', name: 'car_article_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        //1️ Instancier l'entité
        $carArticle = new CarArticle();

        //2️ Créer le formulaire à partir du Type
        $form = $this->createForm(CarArticleType::class, $carArticle);

        //3️ Ecouter la requête
        $form->handleRequest($request);

        //4️ Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Persister l'article en base de données
            $em->persist($carArticle);
            $em->flush();

            // Message de confirmation
            $this->addFlash('success', 'L’article a été créé avec succès !');

            // Redirection vers la page d'accueil
            return $this->redirectToRoute('home');
        }

        // Debug Symfony Profiler
        dump($carArticle);

        // Rendu du template
        return $this->render('admin/car_article_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
