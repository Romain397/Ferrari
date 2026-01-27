<?php

namespace App\Controller;

use App\Entity\Post;
use App\Config\Category;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
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
    public function managePosts(ManagerRegistry $doctrine, Request $request): Response
    {
        $posts = $doctrine->getRepository(Post::class)->findAll();

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    // ───────────── COURSES / SPORT AUTO ─────────────
    #[Route('/sport-auto', name: 'sport_auto')]
    public function sportAutoIndex(ManagerRegistry $doctrine): Response
    {
        // Récupérer uniquement les posts de la catégorie VOITURE (ancien SportAuto)
        $races = $doctrine->getRepository(Post::class)->findBy(
            ['category' => Category::Voiture],
            ['createdAt' => 'DESC']
        );

        return $this->render('sport_auto/index.html.twig', [
            'races' => $races
        ]);
    }
}
