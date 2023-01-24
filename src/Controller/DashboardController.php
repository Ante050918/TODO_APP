<?php

namespace App\Controller;
use App\Entity\TodoList;
use App\Form\TodoListFormType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelper;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class DashboardController extends BaseController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine){

        $this->doctrine = $doctrine;
    }

    #[Route('/dashboard', name: 'app_dashboard_homepage')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function homePage(VerifyEmailHelperInterface $verifyEmailHelper,Request $request, TodoListRepository $repository, TaskRepository $task, TodoList $list, AuthenticationUtils $authenticationUtils){
        $user = $this->getUser();
        $id = $user->getId();

        if(isset($_POST['submit'])){
            $orderBy = $_REQUEST['orderBy'];
            $search= $_REQUEST['search'];
            if($search){
                $todoLists = $repository->search($user, strtolower($search));
                return $this->render('dashboard/dashboard.html.twig',[
                    'todoList' => $todoLists,
                ]);
            }
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
                $this->addFlash('error', 'You have to validate your email first');
                return $this->render('security/login.html.twig');
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
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_registration_verifyuseremail',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );
            $this->addFlash('error', 'You have to validate your email first. Confirm it at: ' .$signatureComponents->getSignedUrl());
            return $this->render('security/login.html.twig', [
                'error' => $authenticationUtils->getLastAuthenticationError(),
                'last_username' => $authenticationUtils->getLastUsername()
            ]);
        }

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