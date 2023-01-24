<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Form\TaskFormType;
use App\Repository\TodoListRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddTaskController extends BaseController
{

    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry){

        $this->registry = $registry;
    }
    #[Route('/dashboard/showTasks/addTask/{id}', name: 'app_task_addtask')]
    public function addTask(Request $request, TodoList $list, TodoListRepository $repository): Response {
        $id = $list->getId();
        $todoList = $repository->find($id);
        $task = new Task();
        $formTask = $this->createForm(TaskFormType::class, $task);
        $formTask->handleRequest($request);

        if ($formTask->isSubmitted() && $formTask->isValid()) {
            $task->setTodoList($todoList);
            $task->setName($formTask->get('name')->getData());
            $task->setPriority($formTask->get('priority')->getData());
            $task->setDeadline($formTask->get('deadline')->getData());
            $entityManager = $this->registry->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard_homepage');
        }

        return $this->render('task/addTask.html.twig',[
                'formTask' => $formTask->createView(),
            ]
        );
    }
}