<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomePageController
{
    private Environment $twig;

    public function __construct(Environment $twig){

        $this->twig = $twig;
    }
    #[Route('/', name: 'app_homepage_homepage')]
    public function homePage(): Response{
        return new Response( $this->twig->render('base.html.twig'));
    }
}