<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class DashboardController extends BaseController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine){

        $this->doctrine = $doctrine;
    }

    #[Route('/dashboard', name: 'app_dashboard_homepage')]
    public function homePage(Request $request){
        $user = $this->getUser();
        $entityManager = $this->doctrine->getManager();
        if($this->isGranted("IS_AUTHENTICATED_FULLY")){
            $user->setStatus("Active");
            $date = new \DateTimeImmutable("now");
            $user->setLastLoginAt($date);
            $entityManager->flush();
        }

        return $this->render('dashboard/dashboard.html.twig');
    }

    #[Route('dashboard/new', name: 'app_dashboard_new')]
    public function new(){
        $this->denyAccessUnlessGranted('ROLE_USER');
        return new Response("kahvbkja");
    }
}