<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/{name}")
     * @return Response
     */
    public function home($name): Response
    {
        return $this->render('home/home.html.twig',['name' => $name]);
    }
}