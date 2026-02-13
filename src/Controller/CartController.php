<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/panier', name: 'cart_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig');
    }

    #[Route('/panier/commander', name: 'cart_checkout', methods: ['POST'])]
    public function checkout(
        Request $request,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    ): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('cart_checkout', (string) $request->request->get('_token'))) {
            $this->addFlash('success', 'Requete invalide.');
            return $this->redirectToRoute('cart_index');
        }

        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->addFlash('success', 'Connectez-vous pour passer votre commande.');
            return $this->redirectToRoute('app_login');
        }

        $rawItems = $request->request->get('cart_items', '[]');
        $items = json_decode($rawItems, true);

        if (!is_array($items) || $items === []) {
            $this->addFlash('success', 'Votre panier est vide.');
            return $this->redirectToRoute('cart_index');
        }

        $total = 0.0;
        $normalizedItems = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $id = (int) ($item['id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($id <= 0 || $quantity <= 0) {
                continue;
            }

            $product = $productRepository->find($id);
            if ($product === null) {
                continue;
            }

            $name = (string) $product->getName();
            $price = (float) $product->getPrice();
            $image = (string) $product->getImage();
            $lineTotal = $price * $quantity;
            $total += $lineTotal;

            $normalizedItems[] = [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $image,
                'lineTotal' => $lineTotal,
            ];
        }

        if ($normalizedItems === []) {
            $this->addFlash('success', 'Votre panier est vide.');
            return $this->redirectToRoute('cart_index');
        }

        $commande = new Commande();
        $commande->setUser($user);
        $commande->setItems($normalizedItems);
        $commande->setTotal($total);

        $entityManager->persist($commande);
        $entityManager->flush();

        $this->addFlash('success', 'Commande enregistrée avec succès.');

        return $this->redirectToRoute('cart_index', ['ordered' => 1]);
    }
}
