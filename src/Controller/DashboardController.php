<?php

namespace App\Controller;
use App\Repository\TodoListRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
class DashboardController extends BaseController
{
    #[Route('/dashboard', name: 'app_dashboard_homepage')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function homePage(UserRepository $userRepository, Request $request, TodoListRepository $repository): Response{
        $user = $this->getUser();
        $arrayOfData = $this->checkTheSubmittedData($request);
        if($arrayOfData){
            $todoLists = $repository->findAllLists($user, $arrayOfData[0], $arrayOfData[1], strtolower($arrayOfData[2]));

            return $this->render('dashboard/dashboard.html.twig',[
                'todoList' => $todoLists,
            ]);
        }
        $todoLists = $repository->findAllLists($user, $arrayOfData[0], $arrayOfData[1], strtolower($arrayOfData[2]));
        $date = new \DateTimeImmutable("now", new \DateTimeZone("Europe/Berlin"));
        $user->setLastLoginAt($date);
        $userRepository->save($user, true);
        return $this->render('dashboard/dashboard.html.twig',[
            'todoList' => $todoLists,
        ]);

    }

}