<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AppController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request, ArticleRepository $articleRepository): RedirectResponse|Response
    {
        // On récupère l'input de recherche du formulaire
        $searchName = $request->query->get('mot_cle');

        // On recherche un article par son nom
        $articles = $articleRepository->findArticleByName($searchName);

        // Si un article est trouvé
        if ($articles) {

            return $this->render('article/indexSearch.html.twig', [
                'articles' => $articles,
            ]);
        }

        // Sinon, on redirige vers l'index
        return $this->redirectToRoute('article_index');
    }

}