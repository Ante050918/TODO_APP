<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard_homepage')]
    public function homePage(){
        return $this->render('dashboard/dashboard.html.twig');
    }

    #[Route('dashboard/new', name: 'app_dashboard_new')]
    public function new(){
        $this->denyAccessUnlessGranted('ROLE_USER');
        return new Response("kahvbkja");
    }
}