<?php

namespace App\Controller;
use App\Repository\TodoListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
class DashboardController extends BaseController
{
    #[Route('/dashboard', name: 'app_dashboard_homepage')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function homePage( Request $request, TodoListRepository $repository): Response{
        $user = $this->getUser();
        $arrayOfData = $this->checkTheSubmittedData($request);
        if($arrayOfData){
            $todoLists = $repository->findAllLists($user, $arrayOfData[0], $arrayOfData[1], strtolower($arrayOfData[2]));

            return $this->render('dashboard/dashboard.html.twig',[
                'todoList' => $todoLists,
                'user' => $user
            ]);
        }
        $todoLists = $repository->findAllLists($user, $arrayOfData[0], $arrayOfData[1], strtolower($arrayOfData[2]));
        return $this->render('dashboard/dashboard.html.twig',[
            'todoList' => $todoLists,
        ]);

    }

}