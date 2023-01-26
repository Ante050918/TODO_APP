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
    #[Route('/dashboard/removeList/{listId}', name: 'app_dashboard_deletelist')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteList($listId, TodoListRepository $listRepository): Response{
        $user = $this->getUser();
        $list = $listRepository->findOneBy(['id' => $listId]);
        $this->checkUser($user, $list);
        $listRepository->remove($list, true);

        return $this->redirectToRoute('app_dashboard_homepage');
    }

    #[Route('/dashboard/addlist', name: 'app_addlist_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request, TodoListRepository $listRepository): Response {
        $user = $this->getUser();
        $todoList = new TodoList();
        $form = $this->createForm(TodoListFormType::class, $todoList);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $listRepository->save($user, $todoList, true);
            return $this->redirectToRoute('app_dashboard_homepage');
        }

        return $this->render('list/addList.html.twig',[
                'form' => $form->createView(),
            ]
        );
    }
}