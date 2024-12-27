<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleSearch;
use App\Entity\PriceSearch;
use App\Form\ArticleSearchType;
use App\Form\ArticleType;
use App\Form\PriceSearchType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'article_index', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator, Request $request, ArticleRepository $articleRepository): Response
    {
//        $article = $articleRepository->findAll();
//        dd($article);
        $article = $paginator->paginate($articleRepository->findAll(),
            $request->query->getInt('page', 1), // on démarre à la page 1
            3 // on ne veut afficher que 3 articles/page
        );

        $propertySearch = new ArticleSearch();
        $form = $this->createForm(ArticleSearchType::class, $propertySearch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on récupère le nom d'article tapé dans le formulaire
            $name = $propertySearch->getName();

            if ($name !== "")  //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
            {
                $articleSearch = $articleRepository->findBy(['name' => $name]);
                return $this->render('article/indexSearch.html.twig', ['articles' => $articleSearch]);
            }
        }

        return $this->render('article/index.html.twig', ['current_menu' => 'articles', 'form' => $form->createView(), 'articles' => $article]);
    }

    #[Route('/article/new', name: 'article_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function new(Request $request, ArticleRepository $articleRepository): RedirectResponse|Response
    {
        $article = new Article();
        /*        $form = $this->createFormBuilder($article)
                    ->add('name', TextType::class)
                    ->add('price', TextType::class)
                    ->add('save', SubmitType::class, array('label' => 'Ajouter un article')
                    )->getForm();*/
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);
            $this->addFlash('success', "L'article <strong>{$article->getName()}</strong> a bien été enregistré");

            return $this->redirectToRoute('article_index');
        }
        return $this->render('article/new.html.twig', [
            'current_menu' => 'articles',
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/{id}', name: 'article_show', methods: ['GET'])]
    #[IsGranted('ROLE_EDITOR')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', array('article' => $article));
    }

    #[Route('/article/edit/{id}', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): RedirectResponse|Response
    {
        /*        $form = $this->createFormBuilder($article)
                    ->add('name', TextType::class)
                    ->add('price', TextType::class)
                    ->add('save', SubmitType::class, array('label' => 'Modifier'))
                    ->getForm();*/

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);
            $this->addFlash('success', "L'article <strong>{$article->getName()}</strong> a bien été modifié");
            return $this->redirectToRoute('article_index');
        }
        return $this->render('article/edit.html.twig', [
            'current_menu' => 'articles',
            'form' => $form->createView()
        ]);
    }

    #[Route('/art_price/', name: 'article_by_price', methods: ['GET','POST'])]
    public function articleByPrice(Request $request, ArticleRepository $articleRepository): Response
    {
        $priceSearch = new PriceSearch();
        $form = $this->createForm(PriceSearchType::class, $priceSearch);
        $form->handleRequest($request);
        $articles = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $minPrice = $priceSearch->getMinPrice();
            $maxPrice = $priceSearch->getMaxPrice();
            $articles = $articleRepository->findByPriceRange($minPrice, $maxPrice);
        }
        return $this->render('article/articleByPrice.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }

    #[Route('/article/delete/{id}', name: 'article_delete', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
            $this->addFlash('success', "L'article <strong>{$article->getName()}</strong> a bien été supprimé");
        }

        return $this->redirectToRoute('article_index');
    }
}