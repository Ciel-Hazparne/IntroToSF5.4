<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/article/new', name: 'article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository): RedirectResponse|Response
    {
        $article = new Article();
/*        $form = $this->createFormBuilder($article)
            ->add('name', TextType::class)
            ->add('price', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Ajouter un article')
            )->getForm();*/
        $form = $this->createForm(ArticleType::class,$article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);

            return $this->redirectToRoute('article_index');
        }
        return $this->render('article/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/article/{id}', name: 'article_show', methods: ['GET'])]
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

        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);
            return $this->redirectToRoute('article_index');
        }
        return $this->render('article/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/article/delete/{id}', name: 'article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('article_index');
    }
}