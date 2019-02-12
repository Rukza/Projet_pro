<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class LoginoutController extends AbstractController
{
    /**
     * Permet l'affichage et le formulaire de connexion'
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
                'hasError' => $error !== null,
                'username' => $username
        ]);
        
    }
    /**
     * Permet gérer la déconnection 
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout(){
            //
    }
}