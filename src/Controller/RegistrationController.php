<?php

namespace App\Controller;

use App\Entity\User;
use App\Notification\Mailer;
use App\Form\Registration\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;



class RegistrationController extends AbstractController
{

     /**
     * Allow to show the form for registred an account
     * 
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, Mailer $mailer, TokenGeneratorInterface $tokenGenerator ){
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()){

        $hash = $encoder->encodePassword($user, $user->getPswd());
        $user->setPswd($hash);
        $user->setToken($tokenGenerator->generateToken());

        $manager->persist($user);
        $manager->flush();

        $bodyMail = $mailer->createBodyMail('emails/confirmation.html.twig', [
            'user' => $user
            ]);
            $mailer->sendMessage('noreply@wristband.com', $user->getEmail(), 'Validation du compte', $bodyMail);
        $this->addFlash(
            'success',
            "votre compte a bien été crée! Un email vas vous être envoyé afin de confirmer votre compte"

            
        );
        return $this->redirectToRoute('account_login');
        }

        return $this->render('registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

     /**
     * Set account to activate if the user have step the mail notification
     * 
     * @Route("account/validate/{id}/{token}", name="validate")
     */
    public function resetting(User $user, $token, ObjectManager $manager,  Request $request)
    {
      
        if ($user->getToken() === null || $token !== $user->getToken())
        {
            throw new AccessDeniedHttpException();
        }
            
            $user->setToken(null);
            $user->setActive(true);
           

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $request->getSession()->getFlashBag()->add('success', "Votre compte a été validé.");

            return $this->redirectToRoute('account_login');

        }
    }
