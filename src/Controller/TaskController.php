<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends BaseController
{
    #[Route('/dashboard/showTasks/{listId}', name: 'app_task_showtasks')]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function showTasks(Request $request, $listId, TaskRepository $taskRepository, TodoListRepository $listRepository): Response{
        $user= $this->getUser();
        $list = $listRepository->findOneBy(['id' => $listId]);
        $this->checkUser($user, $list);
        $arrayOfData = $this->checkTheSubmittedData($request);
        $uncompletedTasks = $taskRepository->findUncompletedTasks('Uncompleted', $list);
        $numberOfUncompletedTasks = count($uncompletedTasks);
        $allTasks = $taskRepository->findAllTasks($list, $arrayOfData[0], $arrayOfData[1], strtolower($arrayOfData[2]));

        return $this->checkCountOfTasksAndRenderTemplate($list, $allTasks, $numberOfUncompletedTasks, $uncompletedTasks);
    }

    #[Route('/dashboard/showTasks/deleteTask/{taskId}/{listId}', name: 'app_task_deletetask')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteTask($taskId, $listId, TaskRepository $repository, TodoListRepository $listRepository): Response{
        $user= $this->getUser();
        $task = $repository->find($taskId);
        $list = $listRepository->findOneBy(['id' => $listId]);
        $this->checkUser($user, $list);
        $repository->remove($task, true);

        return $this->redirectToRoute('app_task_showtasks', ['listId' => $list->getId()]);

    }

    #[Route('/dashboard/showTasks/changeStatus/{taskId}/{listId}', name: "app_task_changestatus")]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changeStatus($taskId, $listId, TaskRepository $repository, TodoListRepository $listRepository): Response{
        $user = $this->getUser();
        $list = $listRepository->find($listId);
        $task = $repository->find($taskId);
        $this->checkUser($user, $list);
        $task->setStatus('Completed');
        $repository->save($task, true);

        return $this->redirectToRoute('app_task_showtasks', ['listId' => $list->getId()]);
    }

    #[Route('/dashboard/showTasks/addTask/{listId}', name: 'app_task_addtask')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addTask($listId, Request $request,TaskRepository $taskRepository, TodoListRepository $listRepository): Response {
        $list = $listRepository->find($listId);
        $task = new Task();
        $task->setTodoList($list);
        $formTask = $this->createForm(TaskFormType::class, $task);
        $formTask->handleRequest($request);
        if ($formTask->isSubmitted() && $formTask->isValid()) {
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_showtasks', ['listId' => $list->getId()]);
        }

        return $this->render('task/addTask.html.twig',[
                'formTask' => $formTask->createView(),
            ]
        );
    }

    #[Route('/dashboard/showTasks/editTask/{taskId}/{listId}', name: 'app_edittask_edittask')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function editTask($taskId, $listId, Request $request, TaskRepository $repository, TodoListRepository $listRepository): Response {
        $task = $repository->find($taskId);
        $list = $listRepository->find($listId);
        $formTaskEdit = $this->createForm(TaskFormType::class, $task);
        $formTaskEdit->handleRequest($request);
        if ($formTaskEdit->isSubmitted() && $formTaskEdit->isValid()) {
            $repository->save($task, true);

            return $this->redirectToRoute('app_task_showtasks', ['listId' => $list->getId()]);
        }

        return $this->render('task/editTask.html.twig',[
                'formTaskEdit' => $formTaskEdit->createView(),
            ]
        );
    }

    public function checkUser($user, $list){
        if(!($list->getUser() === $user)){
            throw $this->createNotFoundException('This user doesn\'t have this task');
        }
    }

    public function checkCountOfTasksAndRenderTemplate($list, $allTasks, $numberOfUncompletedTasks, $uncompletedTasks): Response{
        if(count($allTasks) > 0){
            $completedTasks = count($allTasks) - $numberOfUncompletedTasks;
            $percentage = ($completedTasks / count($allTasks)) * 100;

            return $this->render("task/task.html.twig",[
                "tasks" => $allTasks,
                "list" => $list,
                "uncompleted" => $uncompletedTasks,
                "percentage" => $percentage,
                "time" => new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin')),
            ]);
        }
        else{
            return $this->render("task/task.html.twig",[
                "tasks" => $allTasks,
                "list" => $list,
                "uncompleted" => $uncompletedTasks,
            ]);
        }
    }
}