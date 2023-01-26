<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{

    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry){

        $this->registry = $registry;
    }

    #[Route('/login', name: 'app_security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig',[
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername()
        ]);
    }

    #[Route('/login/setLastLogin', name:"app_security_afterlogin")]
    public function afterLogin(UserRepository $userRepository): Response{
        $user = $this->getUser();
        if($userRepository->find($user)){
            $user->setLastLoginAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin')));
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_dashboard_homepage');
        }

    }

    #[Route('/dashboard/logout', name: 'app_security_logout')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function logout(Security $security)
    {
        throw new \Exception('logout() should never be reached');
    }


}
