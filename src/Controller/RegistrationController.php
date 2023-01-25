<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;


class RegistrationController extends BaseController
{

    #[Route("/register", name: "app_registration_register")]

    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $doctrine, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $registrationDate = new \DateTimeImmutable("now");
            $user->setRegisteredAt($registrationDate);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_registration_verifyuseremail',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            // TODO: in a real app, send this as an email!
            $this->addFlash('success', 'Confirm your email at: '.$signatureComponents->getSignedUrl());
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_dashboard_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify', name: 'app_registration_verifyuseremail')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, ManagerRegistry $registry){
        $user = $userRepository->find($request->query->get('id'));
        if(!$user){
            throw $this->createNotFoundException();
        }
        try{
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
        }catch(VerifyEmailExceptionInterface $e){
            $this->addFlash('error', $e->getReason());

            return $this->redirectToRoute('app_registration_register');
        };
        $user->setIsVerified(true);
        $entityManager = $registry->getManager();
        $entityManager->flush();

        $this->addFlash('success', 'Account Verified! You can now log in.');

        return $this->redirectToRoute('app_security_login');
    }


    #[Route("/verify/resend/{id}", name: "app_verify_resend_email")]

    public function resendVerifyEmail($id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        return $this->render('registration/resend_verify_email.html.twig',[
            'user' => $user,
        ]);
    }

    #[Route("/verify/newVerificationMail/{userId}", name: "app_registration_newverificationemail")]
    public function newVerificationEmail($userId,UserRepository $userRepository, Request $request, VerifyEmailHelperInterface $verifyEmailHelper): Response{
        $user = $userRepository->find($userId);
        $signatureComponents = $verifyEmailHelper->generateSignature(
            'app_registration_verifyuseremail',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        // TODO: in a real app, send this as an email!
        $this->addFlash('success', 'Confirm your email at: '.$signatureComponents->getSignedUrl());
        // do anything else you need here, like send an email

        return $this->redirectToRoute('app_dashboard_homepage');
    }
}