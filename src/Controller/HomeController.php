<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Display the home page
     * 
     * @Route("/", name="homepage")
     */
    public function home()
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * Display the général mention page
     * 
     * @Route("/generalmention", name="general_mention")
     */
    public function generaMention(){
        return $this->render('generalmention.html.twig');
    }
}
