<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditTaskController extends BaseController
{

    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry){

        $this->registry = $registry;
    }
    #[Route('/dashboard/showTasks/editTask/{name}', name: 'app_edittask_edittask')]
    public function editTask(Request $request, Task $task, TaskRepository $repository): Response {
        $id = $task->getId();
        $t = $repository->find($id);
        $formTaskEdit = $this->createForm(TaskFormType::class, $t);
        $formTaskEdit->handleRequest($request);

        if ($formTaskEdit->isSubmitted() && $formTaskEdit->isValid()) {
            $task->setName($formTaskEdit->get('name')->getData());
            $task->setPriority($formTaskEdit->get('priority')->getData());
            $task->setDeadline($formTaskEdit->get('deadline')->getData());
            $entityManager = $this->registry->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard_homepage');
        }

        return $this->render('task/editTask.html.twig',[
                'formTaskEdit' => $formTaskEdit->createView(),
            ]
        );
    }
}