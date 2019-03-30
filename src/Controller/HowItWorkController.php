<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HowItWorkController extends AbstractController
{
    /**
     * Display the page how work the wristlet
     * 
     * @Route("/howitwork", name="how_it_work")
     */
    public function index()
    {
        return $this->render('howitwork/index.html.twig', [
            'controller_name' => 'HowItWorkController',
        ]);
    }
}
