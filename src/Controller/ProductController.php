<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    // ───────────── Liste des produits ─────────────
    #[Route('/store', name: 'store')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository(Product::class)->findAll();

        return $this->render('store/index.html.twig', [
            'products' => $products
        ]);
    }

    // ───────────── Ajouter un produit ─────────────
    #[Route('/store/create', name: 'store_create')]
    public function create(ManagerRegistry $doctrine, Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($this->getUser()); // associe le produit à l'utilisateur
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');
            return $this->redirectToRoute('store_index');
        }

        return $this->render('store/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // ───────────── Supprimer un produit ─────────────
    #[Route('/store/delete/{id}', name: 'store_delete')]
    public function delete(ManagerRegistry $doctrine, Product $product): Response
    {
        $em = $doctrine->getManager();
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé avec succès !');
        return $this->redirectToRoute('store_index');
    }

    // ───────────── Modifier un produit ─────────────
    #[Route('/store/edit/{id}', name: 'store_edit')]
    public function edit(ManagerRegistry $doctrine, Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('store_index');
        }

        return $this->render('store/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }
}
