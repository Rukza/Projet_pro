<?php

namespace App\Controller;

use App\Entity\SerialNumber;
use App\Repository\SerialNumberRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WristletAdminController extends AbstractController
{
/**
     *Permet d'afficher le formulaire pour lier un bracelet au compte
     *
     * @Route("/account/administration", name="wristlet_adminitration")
     * @IsGranted("ROLE_MOTHER")
     * @return Response
     */

    public function adminWristlet(SerialNumberRepository $repo)
    {
       
        return $this->render('account/administration.html.twig',[
            'zob' => $repo->findAll()
        ]);
    }
}