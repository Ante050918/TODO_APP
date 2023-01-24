<?php

namespace App\Controller;
use App\Entity\TodoList;
use App\Entity\User;
use App\Form\TodoListFormType;
use App\Repository\TodoListRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddListController extends BaseController
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry){

        $this->registry = $registry;
    }
    #[Route('/dashboard/addlist', name: 'app_addlist_add')]
    public function add(Request $request): Response {
        $user = $this->getUser();
        $id = $user->getId();
        $todoList = new TodoList();
        $form = $this->createForm(TodoListFormType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todoList->setName($form->get('name')->getData());
            $todoList->setUser($user);
            $createdAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin'));
            $todoList->setCreatedAt($createdAt);
            $entityManager = $this->registry->getManager();
            $entityManager->persist($todoList);
            $entityManager->flush();


            return $this->redirectToRoute('app_dashboard_homepage');
        }

        return $this->render('list/addList.html.twig',[
            'form' => $form->createView(),
        ]
        );
    }
}