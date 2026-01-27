<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    // ───────────── Gérer les utilisateurs ─────────────
    #[Route('/admin/users', name: 'manage_users')]
    public function manage(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();

        return $this->render('admin/manage_users.html.twig', [
            'users' => $users,
        ]);
    }

    // ───────────── Changer le rôle d'un utilisateur ─────────────
    #[Route('/admin/users/role/{id}', name: 'user_role')]
    public function changeRole(ManagerRegistry $doctrine, User $user, Request $request): Response
    {
        $em = $doctrine->getManager();
        $currentRoles = $user->getRoles();

        if ($request->isMethod('POST')) {
            $newRole = $request->request->get('role');

            if (in_array($newRole, ['ROLE_USER', 'ROLE_ADMIN'])) {
                // Remplacer le rôle
                if (in_array('ROLE_ADMIN', $currentRoles)) {
                    $user->setRoles(['ROLE_USER']);
                } else {
                    $user->setRoles(['ROLE_ADMIN']);
                }

                $em->flush();
                $this->addFlash('success', 'Rôle modifié avec succès !');
            }

            return $this->redirectToRoute('manage_users');
        }

        return $this->render('admin/manage_users.html.twig', [
            'users' => $doctrine->getRepository(User::class)->findAll(),
        ]);
    }

    // ───────────── Supprimer un utilisateur ─────────────
    #[Route('/admin/users/delete/{id}', name: 'delete_user')]
    public function delete(ManagerRegistry $doctrine, User $user): Response
    {
        $em = $doctrine->getManager();
        $currentUser = $this->getUser();

        // Vérifier que l'utilisateur ne supprime pas son propre compte
        if ($currentUser instanceof User && $user->getId() === $currentUser->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte !');
            return $this->redirectToRoute('manage_users');
        }

        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'Utilisateur supprimé avec succès !');

        return $this->redirectToRoute('manage_users');
    }
}
