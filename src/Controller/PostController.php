<?php

namespace App\Controller;

use App\Entity\Post;
use App\Config\Category;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    private const POST_UPLOAD_DIR = 'uploads/posts';

    // ───────────── HOME ─────────────
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Récupérer tous les posts de la catégorie VOITURE
        $posts = $doctrine->getRepository(Post::class)->findBy(
            ['category' => Category::Voiture],
            ['highlight' => 'DESC', 'createdAt' => 'DESC']
        );

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    // ───────────── DASHBOARD ─────────────
    #[Route('/admin', name: 'dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    // ───────────── POSTS CRUD ─────────────
    #[Route('/admin/post/manage', name: 'manage_posts')]
    public function managePosts(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $posts = $doctrine->getRepository(Post::class)->findAll();

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $imageFile = $form->get('imageFile')->getData();
            $circuitImageFile = $form->get('circuitImageFile')->getData();
            $this->validatePostImageInputs($post, $form, $imageFile, $circuitImageFile);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->applyPostImageUploads($post, $form->get('imageFile')->getData(), $form->get('circuitImageFile')->getData(), $slugger);

            $em = $doctrine->getManager();
            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Article ajouté avec succès !');
            return $this->redirectToRoute('manage_posts');
        }

        return $this->render('post/manage_posts.html.twig', [
            'posts' => $posts,
            'form' => $form->createView(),
        ]);
    }

    // ───────────── DELETE POST ─────────────
    #[Route('/posts/delete/{id}', name: 'delete_post')]
    public function delete(ManagerRegistry $doctrine, Post $post): Response
    {
        $em = $doctrine->getManager();
        if ($post) {
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Article supprimé avec succès !');
        }

        return $this->redirectToRoute('manage_posts');
    }

    // ───────────── EDIT POST ─────────────
    #[Route('/posts/edit/{id}', name: 'edit_post')]
    public function edit(ManagerRegistry $doctrine, Request $request, Post $post, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $imageFile = $form->get('imageFile')->getData();
            $circuitImageFile = $form->get('circuitImageFile')->getData();
            $this->validatePostImageInputs($post, $form, $imageFile, $circuitImageFile);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->applyPostImageUploads($post, $form->get('imageFile')->getData(), $form->get('circuitImageFile')->getData(), $slugger);

            $em = $doctrine->getManager();
            $em->flush();
            $this->addFlash('success', 'Article modifié avec succès !');
            return $this->redirectToRoute('manage_posts');
        }

        return $this->render('post/manage_posts.html.twig', [
            'posts' => $doctrine->getRepository(Post::class)->findAll(),
            'form' => $form->createView(),
            'editingPost' => $post,
        ]);
    }

    // ───────────── COURSES / SPORT AUTO ─────────────
    #[Route('/sport-auto', name: 'sport_auto')]
    public function sportAutoIndex(ManagerRegistry $doctrine): Response
    {
        // Récupérer uniquement les posts de la catégorie VOITURE (ancien SportAuto)
        $races = $doctrine->getRepository(Post::class)->findBy(
            ['category' => Category::Course],
            ['createdAt' => 'DESC']
        );

        return $this->render('sport_auto/index.html.twig', [
            'races' => $races
        ]);
    }

    private function validatePostImageInputs(Post $post, FormInterface $form, mixed $imageFile, mixed $circuitImageFile): void
    {
        if ($imageFile instanceof UploadedFile && !$this->isValidUploadedImage($imageFile)) {
            $form->get('imageFile')->addError(new FormError('Le fichier image voiture est invalide. Formats acceptés : JPG, PNG, WEBP, GIF.'));
        }

        if ($circuitImageFile instanceof UploadedFile && !$this->isValidUploadedImage($circuitImageFile)) {
            $form->get('circuitImageFile')->addError(new FormError('Le fichier image circuit est invalide. Formats acceptés : JPG, PNG, WEBP, GIF.'));
        }

        if ($post->getCategory() === Category::Voiture) {
            $hasUrl = trim((string) $post->getImage()) !== '';
            $hasUpload = $imageFile instanceof UploadedFile;
            if (!$hasUrl && !$hasUpload) {
                $form->get('image')->addError(new FormError('L’image de la voiture est obligatoire (URL web ou fichier local).'));
            }
        }

        if ($post->getCategory() === Category::Course) {
            $hasUrl = trim((string) $post->getCircuitImage()) !== '';
            $hasUpload = $circuitImageFile instanceof UploadedFile;
            if (!$hasUrl && !$hasUpload) {
                $form->get('circuitImage')->addError(new FormError('L’image du circuit est obligatoire (URL web ou fichier local).'));
            }
        }
    }

    private function applyPostImageUploads(Post $post, mixed $imageFile, mixed $circuitImageFile, SluggerInterface $slugger): void
    {
        if ($imageFile instanceof UploadedFile) {
            $post->setImage($this->uploadPostImage($imageFile, $slugger));
        }

        if ($circuitImageFile instanceof UploadedFile) {
            $post->setCircuitImage($this->uploadPostImage($circuitImageFile, $slugger));
        }
    }

    private function uploadPostImage(UploadedFile $uploadedFile, SluggerInterface $slugger): string
    {
        if (!$this->isValidUploadedImage($uploadedFile)) {
            throw new \RuntimeException('Invalid image upload.');
        }

        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = (string) $slugger->slug($originalFilename);
        if ($safeFilename === '') {
            $safeFilename = 'post-image';
        }
        $safeFilename = substr($safeFilename, 0, 40);
        $extension = strtolower((string) pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_EXTENSION));
        if ($extension === '') {
            $extension = 'bin';
        }
        $newFilename = sprintf('%s-%s.%s', $safeFilename, str_replace('.', '', uniqid('', true)), $extension);

        $uploadAbsolutePath = $this->getParameter('kernel.project_dir') . '/public/' . self::POST_UPLOAD_DIR;
        if (!is_dir($uploadAbsolutePath)) {
            mkdir($uploadAbsolutePath, 0775, true);
        }

        $uploadedFile->move($uploadAbsolutePath, $newFilename);

        return self::POST_UPLOAD_DIR . '/' . $newFilename;
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
