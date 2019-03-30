<?php

namespace App\Controller;

use App\Entity\Weared;
use Doctrine\ORM\Query;
use App\Entity\Requested;
use App\Entity\SerialNumber;
use App\Repository\WearedRepository;
use App\Form\Mother\Weared\WearedAddType;
use App\Form\Mother\Weared\WearedEditType;
use App\Repository\SerialNumberRepository;
use App\Form\Mother\Link\LinkedStatuEditType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\WristletLink\SerialNumberRenameType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
  Controller for the features where user who have the ROLE_MOTHER can :

  - display the user mother board and show stats of is wristlet

  - form to modify the name of a selected wristlet

  - show the list of weared wristlet of this user allow to manage
  - form to add a wearer at wristlet user
  - form to modify a wearer
  - feature to allow delete selected wearer

  - show the list of request the user have recive
  - edit the selected request
  - Allow to delete the selected request
*/


class MotherAdminController extends AbstractController
{
    /**
     * Allow to display the user mother board and show stats of is wristlet (TODO move it in a service)
     *
     * @Route("/account/mother/administration", name="wristlet_mother_adminitration")
     * @IsGranted("ROLE_MOTHER")
     * @return Response
     */

    public function adminWristlet(ObjectManager $manager)
    {
        $user = $this->getUser();

        //DQL queris to count the number wristlet the user have
        $queryWristlets = $manager->createQuery('SELECT COUNT(s.attributedTo) FROM App\Entity\SerialNumber s WHERE s.Mother = :user');
        $queryWristlets->setParameter('user', $user);
        $numWristlets = $queryWristlets->getResult(Query::HYDRATE_SINGLE_SCALAR);

        //DQL queris to count the number wristlet who they have not a weared registred
        $queryWears  = $manager->createQueryBuilder();
        $queryWears ->select('COUNT(s.attributedTo)')
           ->from(SerialNumber::class, 's')
           ->where('s.Mother = :user')
           ->setParameter('user', $user)
           ->andWhere('s.attributedTo = :false')
           ->setParameter('false', 0);
        $numWears = $queryWears->getQuery()->getSingleScalarResult();

        //DQL queris to count the number wristlet who they have not named by the user
        $queryNotNamed  = $manager->createQueryBuilder();
        $queryNotNamed ->select('COUNT(s.attributedTo)')
           ->from(SerialNumber::class, 's')
           ->where('s.Mother = :user')
           ->setParameter('user', $user)
           ->andWhere('s.wristletTitle = :NotNamed')
           ->setParameter('NotNamed', "Undefine");
        $numNotNames = $queryNotNamed->getQuery()->getSingleScalarResult();
        
        //DQL queris to count the number request wristlet user have recive
        $queryRequests = $manager->createQuery('SELECT COUNT(r.requestedFor) FROM App\Entity\Requested r WHERE r.requestedFor = :user');
        $queryRequests->setParameter('user', $user);
        $numRequests = $queryRequests->getResult(Query::HYDRATE_SINGLE_SCALAR);
 
        //DQL queris to count if the user have requetet in wait to validate
        $queryWaitingRequests  = $manager->createQueryBuilder();
        $queryWaitingRequests ->select('COUNT(r.requestedFor)')
           ->from(Requested::class, 'r')
           ->where('r.requestedFor = :user')
           ->setParameter('user', $user)
           ->andWhere('r.requestedToken IS NOT NULL');
           $numWaiting = $queryWaitingRequests->getQuery()->getSingleScalarResult();

;        return $this->render('account/mother/administration.html.twig',[
           'user' => $user,
           'stats' => [
           'numRequests' => $numRequests,
           'numWristlets' => $numWristlets,
           'numWears' => $numWears,
           'numWaiting' => $numWaiting,
           'numNotNames' => $numNotNames
           ]
        ]);
    }

    /**
     * Form to modify the name of a selected wristlet
     * 
     * @Route ("/account/mother/{id}/edit", name="mother_Wristlet_Name_edit")
     * @IsGranted("ROLE_MOTHER")
     * @return Response
     */
    public function editWristletName(SerialNumber $requested, ObjectManager $manager, Request $request){
        $form = $this->createForm(SerialNumberRenameType::class,$requested);
        

        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
                $manager->persist($requested);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Le bracelet {$requested->getwristletTitle()} a bien été modfiée !"
                );
            }
            return $this->render('/account/mother/edit.html.twig',[
                'SerialNumber' => $requested,
                'form' => $form->createView()
            ]);
    }

    /**
    * Show the list of weared wristlet of this user allow to manage
    *
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
 
 
        return $this->render('/account/mother/weared/wearedmanagement.html.twig',[
            'mother' => $mother,
            'wears' => $weared
        ]);
    }

    /**
     * Form to add a wearer at wristlet user
     * 
     * @Route("/account/mother/weared/", name="mother_weared_add")
     * @IsGranted("ROLE_MOTHER")
     * @return response 
     */

    public function addWeared(Request $request, ObjectManager $manager){
        $weared = new Weared();
        
        //Send the user at the form to have acces at named wristlet
        $form = $this->createForm(WearedAddType::class, $weared, [
        'user' => $user=$this->getUser()
        ]);
  
     
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $mother = $weared->getWearWristlet();
                       
            $mother->setAttributedTo(true);
            $manager->persist($weared);
            $manager->flush();

                $this->addFlash(
                    'success',
                    "L'utilisateur {$weared->getFullName()} a bien été ajouté et lier au bracelet {$weared->getWearWristlet()}"
                );
        }
            return $this->render('/account/mother/weared/add.html.twig',[
                'wears' => $weared,
                'form' => $form->createView()
            ]);
    }

    /**
     * Form to modify a wearer
     * 
     * @Route ("/account/mother/weared/{id}/edit", name="mother_weared_edit")
     * @IsGranted("ROLE_MOTHER")
     * @return Response
     */
    public function editRequests(Weared $weared, ObjectManager $manager, Request $request){
        $form = $this->createForm(WearedEditType::class,$weared);
        

        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
                $manager->persist($weared);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Le porteur {$weared->getFullName()} de {$weared->getWearWristlet()} a bien été modfiée !"
                );
            }
            return $this->render('/account/mother/weared/edit.html.twig',[
                'wears' => $weared,
                'form' => $form->createView()
            ]);
    }

    /**
     * Allow to delete the selected wearer 
     * 
     * @Route ("/account/mother/weared/{id}/delete", name="mother_weared_delete")
     * @IsGranted("ROLE_MOTHER")
     * @return Response
     */
    public function deleteRequested(Weared $weared, ObjectManager $manager){
        $mother = $weared->getWearWristlet();
        $mother->setAttributedTo(false);
        $manager->remove($weared);
        $manager->flush();
        
        $this->addflash(
            'success',
            "Le porteur a bien été supprimée !"
            
        );
        return $this->redirectToRoute('mother_weared_management');
    }




    /** 
    * Show the list of request the user have recive
    *
    * @Route("/account/mother/linked/linkedmanagement", name="mother_linked_management")
    * @IsGranted("ROLE_MOTHER")
    * @return Response
    */
    public function motherLinkedManagement(){

        $user = $this->getUser();

        $mother = $this->getDoctrine()
        ->getRepository(SerialNumber::class)
        ->findBy(array ('Mother' => $user));
        
        $requested = $this->getDoctrine()
        ->getRepository(Requested::class)
        ->findBy(array ('requestedFor' => $mother));
 
        return $this->render('/account/mother/linked/linkedmanagement.html.twig',[
            'mothers' => $mother,
            'requests' => $requested
        ]);
    }




    /**
     * Allow to edit the selected request
     *
     * @Route ("/account/mother/linked/{id}/edit", name="mother_linked_edit")
     * @IsGranted("ROLE_MOTHER")
     * @return Response
     */
        public function editLinked(Requested $requested, ObjectManager $manager, Request $request){
        $form = $this->createForm(LinkedStatuEditType::class,$requested);
        $form->handleRequest($request); 

        if($form->get('refuser')->isClicked() && $form->isValid())
        {

            $requested->setRequestedAccepted(false);
            $requested->setRequestedRefused(true);
            $requested->setRequestedBanned(false);
            $manager->persist($requested);
            $manager->flush();

             $this->addFlash(
            'success',
            "La demande a bien été modifié, vous l'avez passé a refusée"
            );
        
        }
        if($form->get('accepter')->isClicked() && $form->isValid())
        {

            $requested->setRequestedAccepted(true);
            $requested->setRequestedRefused(false);
            $requested->setRequestedBanned(false);
            $manager->persist($requested);
            $manager->flush();

             $this->addFlash(
            'success',
            "La demande a bien été modifié, vous l'avez passé a accepté"
            );
        
        }
        if($form->get('bannir')->isClicked() && $form->isValid())
        {

            $requested->setRequestedAccepted(false);
            $requested->setRequestedRefused(false);
            $requested->setRequestedBanned(true);
            $manager->persist($requested);
            $manager->flush();

             $this->addFlash(
            'success',
            "La demande a bien été modifié, vous l'avez passé a bloqué"
            );
        
        }
   
            return $this->render('/account/mother/linked/edit.html.twig',[
                'requests' => $requested,
                'form' => $form->createView()
            ]);
        }



    /**
     * Allow to delete the selected request
     * 
     * @Route ("/account/mother/linked/{id}/delete", name="mother_linked_delete")
     * @IsGranted("ROLE_MOTHER")
     * @return Response
     */
    public function deleteLinked(Requested $requested, ObjectManager $manager){
        
        $manager->remove($requested);
        $manager->flush();
        
        $this->addflash(
            'success',
            "La liaison a bien été supprimée !"
            
        );
        return $this->redirectToRoute('mother_linked_management');
    }

}