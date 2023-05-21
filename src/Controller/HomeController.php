<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('home/home.html.twig', ['current_menu' => 'home',]);
    }

//    /**
//     * @Route("/article/new")
//     */
//    public function new(): Response
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//
//        $article = new Article();
//        $article->setName('Article 1');
//        $article->setPrice(1000);
//
//        $entityManager->persist($article);
//        $entityManager->flush();
//
//        return new Response('Article enregistré avec id ' . $article->getId());
//    }
}