<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class LoginoutController extends AbstractController
{
    /**
     * Display the form to connect a registred user
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('super_admin');
        }

        return $this->render('account/login.html.twig', [
                'hasError' => $error !== null,
                'username' => $username
        ]);
        
    }
    /**
     * Allow a logged user to logout
     * 
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout(){
            //
    }
}