<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\SerialNumber;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SerialNumberRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
  Controller for the features for register and connected user

  - list of all wristelet allowed at user account
  - show the selected wristlet heart beat graph
*/


class CardioController extends AbstractController
{
        /**
        * Displays the list of all wristelet allowed at user account
        *
        * @Route("/account/cardio/cardiolist", name="account_cardio")
        * @Security("is_granted('ROLE_USER')")
        * @return Response
        */

        public function cardio( ObjectManager $manager,SerialNumberRepository $repo ){
          
            return $this->render('account/cardio/cardiolist.html.twig');
        }

        /** 
         * Allow to show the selected wristlet heart beat graph
         * 
         * @Route("/account/cardio/cardiowristlet/{id}", name="wristlet")
         * @Security("is_granted('ROLE_MOTHER') or is_granted('ROLE_CHILD')")
         * @return Response
         */

        public function show($id,SerialNumber $serial,SerialNumberRepository $repo){
            // TODO Graph in backend            
            $serial = $repo->findOneByid($id);
        
            return $this->render('account/cardio/cardio.html.twig',[
                'serial' => $serial
            ]);

        }
}