<?php

namespace App\Controller;

use App\Config\Type;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    // ───────────── Liste des produits (publique) ─────────────
    #[Route('/store', name: 'store')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $minRaw = trim((string) $request->query->get('min_price', ''));
        $maxRaw = trim((string) $request->query->get('max_price', ''));

        $filters = [
            'q' => (string) $request->query->get('q', ''),
            'type' => (string) $request->query->get('type', ''),
            'min_price' => $minRaw !== '' ? (float) $minRaw : null,
            'max_price' => $maxRaw !== '' ? (float) $maxRaw : null,
            'sort' => (string) $request->query->get('sort', 'recent'),
        ];

        if ($filters['min_price'] !== null && $filters['min_price'] < 0) {
            $filters['min_price'] = 0.0;
        }

        if ($filters['max_price'] !== null && $filters['max_price'] < 0) {
            $filters['max_price'] = null;
        }

        if ($filters['min_price'] !== null && $filters['max_price'] !== null && $filters['min_price'] > $filters['max_price']) {
            [$filters['min_price'], $filters['max_price']] = [$filters['max_price'], $filters['min_price']];
        }

        $products = $productRepository->findForStore($filters);

        $typeOptions = [];
        foreach (Type::cases() as $case) {
            $typeOptions[$case->value] = match ($case->value) {
                'merch' => 'Merchandising',
                'accessoire' => 'Accessoire',
                'vetement' => 'Vêtement',
                default => ucfirst($case->value),
            };
        }

        return $this->render('store/index.html.twig', [
            'products' => $products,
            'filters' => $filters,
            'typeOptions' => $typeOptions,
        ]);
    }

    // ───────────── Gérer les produits (admin) ─────────────
    #[Route('/admin/store/manage', name: 'manage_store')]
    public function manage(ManagerRegistry $doctrine, Request $request): Response
    {
        $products = $doctrine->getRepository(Product::class)->findAll();

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($this->getUser());
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');
            return $this->redirectToRoute('manage_store');
        }

        return $this->render('admin/manage_store.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
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
            return $this->redirectToRoute('store');
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
        return $this->redirectToRoute('manage_store');
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
            return $this->redirectToRoute('manage_store');
        }

        return $this->render('admin/manage_store_edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }
}

