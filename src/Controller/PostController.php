<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer tous les articles depuis la base
        $posts = $em->getRepository(Post::class)->findBy(
            ["category" => "Voiture"],
            ['highlight' => 'DESC', 'date' => 'DESC'] // mettre à la une puis plus récent
        );

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    // --------------------------
    // DASHBOARD
    // --------------------------
    #[Route('/admin', name: 'dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    // --------------------------
    // POSTS CRUD
    // --------------------------
    #[Route('/admin/post/create', name: 'manage_posts')]
    public function manageCarArticles(ManagerRegistry $doctrine, Request $request): Response
    {
        $posts = $doctrine->getRepository(Post::class)->findAll();

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Article ajouté avec succès !');
            return $this->redirectToRoute('admin_manage_posts');
        }

        return $this->render('admin/manage_posts.html.twig', [
            'posts' => $posts,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/posts/delete/{id}', name: 'delete_post')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $article = $em->getRepository(Post::class)->find($id);

        if ($article) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', 'Article supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_manage_posts');
    }
}
