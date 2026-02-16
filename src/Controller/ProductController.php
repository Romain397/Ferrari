<?php

namespace App\Controller;

use App\Config\Type;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    private const PRODUCT_UPLOAD_DIR = 'uploads/products';

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
    public function manage(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $products = $doctrine->getRepository(Product::class)->findAll();

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resolvedImage = $this->resolveProductImageInput(
                $form->get('imageFile')->getData(),
                (string) $form->get('imageUrl')->getData(),
                $slugger
            );

            if ($resolvedImage === null) {
                $this->addFlash('warning', 'Veuillez fournir une image (fichier local ou URL web valide).');
                return $this->redirectToRoute('manage_store');
            }
            $product->setImage($resolvedImage);

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
    public function create(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resolvedImage = $this->resolveProductImageInput(
                $form->get('imageFile')->getData(),
                (string) $form->get('imageUrl')->getData(),
                $slugger
            );

            if ($resolvedImage === null) {
                $this->addFlash('warning', 'Veuillez fournir une image (fichier local ou URL web valide).');
                return $this->redirectToRoute('store_create');
            }
            $product->setImage($resolvedImage);

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
    public function edit(ManagerRegistry $doctrine, Request $request, Product $product, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resolvedImage = $this->resolveProductImageInput(
                $form->get('imageFile')->getData(),
                (string) $form->get('imageUrl')->getData(),
                $slugger
            );
            if ($resolvedImage !== null) {
                $product->setImage($resolvedImage);
            }

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

    private function uploadProductImage(UploadedFile $uploadedFile, SluggerInterface $slugger): string
    {
        if (!$this->isValidUploadedImage($uploadedFile)) {
            throw new \RuntimeException('Invalid image upload.');
        }

        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = (string) $slugger->slug($originalFilename);
        if ($safeFilename === '') {
            $safeFilename = 'product-image';
        }
        $safeFilename = substr($safeFilename, 0, 40);
        $extension = strtolower((string) pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_EXTENSION));
        if ($extension === '') {
            $extension = 'bin';
        }
        $newFilename = sprintf('%s-%s.%s', $safeFilename, str_replace('.', '', uniqid('', true)), $extension);

        $uploadAbsolutePath = $this->getParameter('kernel.project_dir') . '/public/' . self::PRODUCT_UPLOAD_DIR;
        if (!is_dir($uploadAbsolutePath)) {
            mkdir($uploadAbsolutePath, 0775, true);
        }

        $uploadedFile->move($uploadAbsolutePath, $newFilename);

        return self::PRODUCT_UPLOAD_DIR . '/' . $newFilename;
    }

    private function resolveProductImageInput(mixed $uploadedFile, string $imageUrl, SluggerInterface $slugger): ?string
    {
        if ($uploadedFile instanceof UploadedFile) {
            if (!$this->isValidUploadedImage($uploadedFile)) {
                return null;
            }

            return $this->uploadProductImage($uploadedFile, $slugger);
        }

        $candidate = trim($imageUrl);
        if ($candidate === '') {
            return null;
        }

        if (filter_var($candidate, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $scheme = strtolower((string) parse_url($candidate, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $candidate;
    }

    private function isValidUploadedImage(UploadedFile $uploadedFile): bool
    {
        $imageInfo = @getimagesize($uploadedFile->getPathname());
        if ($imageInfo === false) {
            return false;
        }

        $mime = (string) ($imageInfo['mime'] ?? '');

        return in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true);
    }
}

