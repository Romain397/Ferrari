<?php

namespace App\Controller;

use App\Entity\CarArticle;
use App\Entity\StoreItem;
use App\Entity\SportAuto;
use App\Form\CarArticleType;
use App\Form\StoreItemType;
use App\Form\SportAutoType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    // --------------------------
    // DASHBOARD
    // --------------------------
    #[Route('', name: 'dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    // --------------------------
    // CAR ARTICLES CRUD
    // --------------------------
    #[Route('/car-articles', name: 'manage_car_articles')]
    public function manageCarArticles(ManagerRegistry $doctrine, Request $request): Response
    {
        $articles = $doctrine->getRepository(CarArticle::class)->findAll();

        $carArticle = new CarArticle();
        $form = $this->createForm(CarArticleType::class, $carArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($carArticle);
            $em->flush();

            $this->addFlash('success', 'Article ajouté avec succès !');
            return $this->redirectToRoute('admin_manage_car_articles');
        }

        return $this->render('admin/manage_car_articles.html.twig', [
            'articles' => $articles,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/car-articles/delete/{id}', name: 'delete_car_article')]
    public function deleteCarArticle(ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $article = $em->getRepository(CarArticle::class)->find($id);

        if ($article) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', 'Article supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_manage_car_articles');
    }

    // --------------------------
    // STORE ITEMS CRUD
    // --------------------------
    #[Route('/store', name: 'manage_store')]
    public function manageStore(ManagerRegistry $doctrine, Request $request): Response
    {
        $products = $doctrine->getRepository(StoreItem::class)->findAll();

        $storeItem = new StoreItem();
        $form = $this->createForm(StoreItemType::class, $storeItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($storeItem);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');
            return $this->redirectToRoute('admin_manage_store');
        }

        return $this->render('admin/manage_store.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/store/delete/{id}', name: 'delete_store_item')]
    public function deleteStoreItem(ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $item = $em->getRepository(StoreItem::class)->find($id);

        if ($item) {
            $em->remove($item);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_manage_store');
    }

    // --------------------------
    // SPORT AUTO CRUD
    // --------------------------
    #[Route('/sport-auto', name: 'manage_sport_auto')]
    public function manageSportAuto(ManagerRegistry $doctrine, Request $request): Response
    {
        $races = $doctrine->getRepository(SportAuto::class)->findAll();

        $sportAuto = new SportAuto();
        $form = $this->createForm(SportAutoType::class, $sportAuto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($sportAuto);
            $em->flush();

            $this->addFlash('success', 'Événement ajouté avec succès !');
            return $this->redirectToRoute('admin_manage_sport_auto');
        }

        return $this->render('admin/manage_sport_auto.html.twig', [
            'races' => $races,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sport-auto/delete/{id}', name: 'delete_sport_auto')]
    public function deleteSportAuto(ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $race = $em->getRepository(SportAuto::class)->find($id);

        if ($race) {
            $em->remove($race);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_manage_sport_auto');
    }
}
