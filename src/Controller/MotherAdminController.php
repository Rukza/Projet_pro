<?php

namespace App\Controller;

use App\Entity\Weared;
use App\Form\WearedAddType;
use App\Entity\SerialNumber;
use App\Repository\WearedRepository;
use App\Repository\SerialNumberRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MotherAdminController extends AbstractController
{
    /**
     *Permet d'afficher la page d'aministration d'un compte mother
     *
     * @Route("/account/mother/administration", name="wristlet_mother_adminitration")
     * @IsGranted("ROLE_MOTHER")
     * @return Response
     */

    public function adminWristlet(WearedRepository $repo)
    {
       
      
        return $this->render('account/mother/administration.html.twig',[
           
        ]);
    }

    /**
     * Affiche la liste des bracelets du compte mother
    * @Route("/account/mother/weared/wearedmanagement", name="mother_weared_management")
    * @IsGranted("ROLE_MOTHER")
    * @return Response
    */
    public function motherWearedManagement(){

        $user = $this->getUser();

        $mother = $this->getDoctrine()
        ->getRepository(SerialNumber::class)
        ->findBy(array ('Mother' => $user));
        
        $weared = $this->getDoctrine()
        ->getRepository(Weared::class)
        ->findBy(array ('wearWristlet' => $mother));
 dump($weared);
 
        return $this->render('/account/mother/weared/wearedmanagement.html.twig',[
            'mother' => $mother,
            'wears' => $weared
        ]);
    }



    







    /**
     * Permet d'ajouter un porteur de bracelet
     * 
     * @Route("/account/mother/weared/", name="mother_weared_add")
     * @IsGranted("ROLE_MOTHER")
     * @return response 
     */

    public function addWeared(Request $request, ObjectManager $manager){
        $weared = new Weared();
        dump($weared);
    $form = $this->createForm(WearedAddType::class, $weared, [
    'user' => $user=$this->getUser()
]);
  
     
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
          
            $manager->persist($weared);
            $manager->flush();

                $this->addFlash(
                    'success',
                    "l'utilisateur {$weared->getFullName()} a bien ajouté !"
                );
            }
            return $this->render('/account/mother/weared/add.html.twig',[
                'wears' => $weared,
                'form' => $form->createView()
            ]);
    }
}