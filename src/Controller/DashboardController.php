<?php

namespace App\Controller;
use App\Entity\TodoList;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
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
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function homePage(Request $request, TodoListRepository $repository, TaskRepository $task, TodoList $list){
        if(isset($_POST['submit'])){
            $orderBy = $_REQUEST['orderBy'];

            $user = $this->getUser();
            $id = $user->getId();
            if($user->isIsVerified() === true){
                $user->setStatus("Active");
                $date = new \DateTimeImmutable("now");
                $user->setLastLoginAt($date);
                $entityManager = $this->doctrine->getManager();
                if(str_contains($orderBy, 'name1')){
                    $orderBy = 'name';
                    $todoLists = $repository->findBy(['user' => strval($id)], [$orderBy => 'ASC']);
                    $entityManager->flush();
                    return $this->render('dashboard/dashboard.html.twig',[
                        'todoList' => $todoLists,
                    ]);
                }
                elseif(str_contains($orderBy, 'name2')){
                    $orderBy = 'name';
                    $todoLists = $repository->findBy(['user' => strval($id)], [$orderBy => 'DESC']);
                    $entityManager->flush();
                    return $this->render('dashboard/dashboard.html.twig',[
                        'todoList' => $todoLists,
                    ]);
                }
                elseif(str_contains($orderBy, 'createdAt1')){
                    $orderBy = 'createdAt';
                    $todoLists = $repository->findBy(['user' => strval($id)], [$orderBy => 'ASC']);
                    $entityManager->flush();
                    return $this->render('dashboard/dashboard.html.twig',[
                        'todoList' => $todoLists,
                    ]);
                }
                elseif(str_contains($orderBy, 'createdAt2')){
                    $orderBy = 'createdAt';
                    $todoLists = $repository->findBy(['user' => strval($id)], [$orderBy => 'DESC']);
                    $entityManager->flush();
                    return $this->render('dashboard/dashboard.html.twig',[
                        'todoList' => $todoLists,
                    ]);
                }
            }
            else{
                return new Response("You must validate your email first!");
            }
        }
        $user = $this->getUser();
        $id = $user->getId();
        if($user->isIsVerified() === true){
            $entityManager = $this->doctrine->getManager();

            $todoLists = $repository->findAllLists($id);
            $user->setStatus("Active");
            $date = new \DateTimeImmutable("now");
            $user->setLastLoginAt($date);
            $entityManager->flush();
            return $this->render('dashboard/dashboard.html.twig',[
                'todoList' => $todoLists,
            ]);
        }
        else{
            return new Response("You must validate your email first!");
        }

    }

    #[Route('dashboard/new', name: 'app_dashboard_new')]
    public function new(){
        $this->denyAccessUnlessGranted('ROLE_USER');
        return new Response("kahvbkja");
    }

    #[Route('/dashboard/removeList/{name}', name: 'app_dashboard_deletelist')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteList(TodoListRepository $repository, TodoList $todoList){

        $user= $this->getUser();
        if($user->isIsVerified() === true){
            $id = $todoList->getId();
            $list = $repository->find($id);
            $repository->remove($list, true);

            return $this->redirectToRoute('app_dashboard_homepage');
        }
        else{
            return $this->redirectToRoute('app_security_login');
        }

    }
}