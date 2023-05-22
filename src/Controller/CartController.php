<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CartController extends AbstractController
{

//    /**
//     * @Route("/cart", name="cart_index")
//     * @param SessionInterface $session
//     * @param ArticleRepository $articleRepository
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
    #[Route('/cart', name: 'cart_index')]
    public function index(SessionInterface $session, ArticleRepository $articleRepository): Response
    {
        $cart = $session->get('cart', []);

        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'id'=>$id,
                'article' => $articleRepository->find($id),
                'quantity' => $quantity
            ];
        }
        //dd($cartWithData);
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData
        ]);
    }


//  /**
//     * @Route("/cart/add/{id}", name="cart_add")
//     * @param $id
//     * @param SessionInterface $session
//     * @return RedirectResponse
//     */
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, SessionInterface $session): RedirectResponse // SessionInterface permet de récupérer la session

    {
        $cart = $session->get('cart', []); // si pas encore de panier dans la session on affecte un panier vide

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart); // on injecte dans la session le panier modifié
        //dd($session->get('cart'));
        return $this->redirectToRoute("article_index");
    }

//    /**
//     * @Route("/cart/remove/{id}", name="cart_remove")
//     * @param $id
//     * @param SessionInterface $session
//     * @return RedirectResponse
//     */
    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, SessionInterface $session): RedirectResponse
    {
        $cart = $session->get('cart', []);
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);
        return $this->redirectToRoute("cart_index");
    }
}