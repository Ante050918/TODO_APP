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
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends BaseController
{

    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry){

        $this->registry = $registry;
    }
    #[Route('/dashboard/showTasks/{id}', name: 'app_task_showtasks')]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function showTasks(TaskRepository $repository, TodoList $todoList, TodoListRepository $listRepository){
        $id = $todoList->getId();
        $list = $listRepository->find($id);
        if(isset($_POST['submit'])){
            $orderBy = $_REQUEST['orderBy'];
            $search = $_REQUEST['search'];
            if($search){
                $tasks = $repository->findUncompletedTasks($id, strtolower($search));

            }
            $uncompletedTasks = $repository->findUncompletedTasks('Uncompleted', $id);
            $number = count($uncompletedTasks);

            $user = $this->getUser();
            if($user->isIsVerified() === true){
                $allTasks = $repository->findBy(['todoList' => strval($id)], [$orderBy => 'ASC']);
                $time = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin'));
                if(count($allTasks) > 0){
                    $completedTasks = count($allTasks) - $number;
                    $percentage = ($completedTasks / count($allTasks)) * 100;
                    return $this->render("task/task.html.twig",[
                        "tasks" => $allTasks,
                        "list" => $list,
                        "uncompleted" => $uncompletedTasks,
                        "percentage" => $percentage,
                        "time" => $time,
                    ]);
                }
                else{
                    return $this->render("task/task.html.twig",[
                        "tasks" => $allTasks,
                        "list" => $list,
                        "uncompleted" => $uncompletedTasks,
                    ]);
                }

        }}
        $id = $todoList->getId();
        $list = $listRepository->find($id);
        $uncompletedTasks = $repository->findUncompletedTasks('Uncompleted', $id);
        $number = count($uncompletedTasks);

        $user = $this->getUser();
        if($user->isIsVerified() === true){
            $allTasks = $repository->findAllTasks($id);
            $time = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin'));
            if(count($allTasks) > 0){
                $completedTasks = count($allTasks) - $number;
                $percentage = ($completedTasks / count($allTasks)) * 100;
                return $this->render("task/task.html.twig",[
                    "tasks" => $allTasks,
                    "list" => $list,
                    "uncompleted" => $uncompletedTasks,
                    "percentage" => $percentage,
                    "time" => $time,
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
        else{
            return $this->redirectToRoute('app_security_login');
        }

    }

    #[Route('/dashboard/showTasks/deleteTask/{name}', name: 'app_task_deletetask')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteTask(TaskRepository $repository, Task $task){
        $user= $this->getUser();
        if($user->isIsVerified() === true){
            $id = $task->getId();
            $t = $repository->find($id);
            $repository->remove($t, true);

            return $this->redirectToRoute('app_dashboard_homepage');
        }
        else{
            return $this->redirectToRoute('app_security_login');
        }

    }

    #[Route('/dashboard/showTasks/changeStatus/{id}', name: "app_task_changestatus")]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changeStatus(Task $task, TaskRepository $repository){
        $task->setStatus('Completed');
        $entityManager = $this->registry->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard_homepage');
    }

    #[Route('/dashboard/showTasks/addTask/{id}', name: 'app_task_addtask')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addTask(Request $request, TodoList $list, TodoListRepository $repository, TaskRepository $taskRepository): Response {
        $id = $list->getId();
        $todoList = $repository->find($id);
        $task = new Task();
        $formTask = $this->createForm(TaskFormType::class, $task);
        $formTask->handleRequest($request);

        if ($formTask->isSubmitted() && $formTask->isValid()) {
            $task = $formTask->getData();
            $taskRepository->edit($todoList, $task, true);
            return $this->redirectToRoute('app_dashboard_homepage');
        }

        return $this->render('task/addTask.html.twig',[
                'formTask' => $formTask->createView(),
            ]
        );
    }

    #[Route('/dashboard/showTasks/editTask/{name}', name: 'app_edittask_edittask')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function editTask(Request $request, Task $task, TaskRepository $repository): Response {
        $id = $task->getId();
        $t = $repository->find($id);
        $formTaskEdit = $this->createForm(TaskFormType::class, $t);
        $formTaskEdit->handleRequest($request);

        if ($formTaskEdit->isSubmitted() && $formTaskEdit->isValid()) {
            $task = $formTaskEdit->getData();
            $repository->save($task, true);
            return $this->redirectToRoute('app_dashboard_homepage');
        }

        return $this->render('task/editTask.html.twig',[
                'formTaskEdit' => $formTaskEdit->createView(),
            ]
        );
    }
}