<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListFormType;
use App\Repository\TodoListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ListController extends BaseController
{
    #[Route('/dashboard/removeList/{name}', name: 'app_dashboard_deletelist')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteList(TodoListRepository $repository, TodoList $todoList): Response{
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

    #[Route('/dashboard/addlist', name: 'app_addlist_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request, TodoListRepository $repository): Response {
        $user = $this->getUser();
        $todoList = new TodoList();
        $form = $this->createForm(TodoListFormType::class, $todoList);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todoList = $form->getData();
            $repository->save($user, $todoList, true);
            return $this->redirectToRoute('app_dashboard_homepage');
        }

        return $this->render('list/addList.html.twig',[
                'form' => $form->createView(),
            ]
        );
    }
}