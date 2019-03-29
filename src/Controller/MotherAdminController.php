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

class MotherAdminController extends AbstractController
{
    /**
     *Permet d'afficher la page d'aministration d'un compte mother
     *
     * @Route("/account/mother/administration", name="wristlet_mother_adminitration")
     * @IsGranted("ROLE_MOTHER")
     * @return Response
     */

    public function adminWristlet(ObjectManager $manager)
    {
        $user = $this->getUser();
        //stats des bracelets totals d'un compte mother
        $queryWristlets = $manager->createQuery('SELECT COUNT(s.attributedTo) FROM App\Entity\SerialNumber s WHERE s.Mother = :user');
        $queryWristlets->setParameter('user', $user);
        $numWristlets = $queryWristlets->getResult(Query::HYDRATE_SINGLE_SCALAR);

        //stats des bracelets qui n'ont pas encore recus de porteur
        $queryWears  = $manager->createQueryBuilder();
        $queryWears ->select('COUNT(s.attributedTo)')
           ->from(SerialNumber::class, 's')
           ->where('s.Mother = :user')
           ->setParameter('user', $user)
           ->andWhere('s.attributedTo = :false')
           ->setParameter('false', 0);
        $numWears = $queryWears->getQuery()->getSingleScalarResult();

        //stats des bracelets qui n'ont pas eu de nom
        $queryNotNamed  = $manager->createQueryBuilder();
        $queryNotNamed ->select('COUNT(s.attributedTo)')
           ->from(SerialNumber::class, 's')
           ->where('s.Mother = :user')
           ->setParameter('user', $user)
           ->andWhere('s.wristletTitle = :NotNamed')
           ->setParameter('NotNamed', "Undefine");
        $numNotNames = $queryNotNamed->getQuery()->getSingleScalarResult();
        
        //stats des requetes pour tout les bracelets d'un compte mother
        $queryRequests = $manager->createQuery('SELECT COUNT(r.requestedFor) FROM App\Entity\Requested r WHERE r.requestedFor = :user');
        $queryRequests->setParameter('user', $user);
        $numRequests = $queryRequests->getResult(Query::HYDRATE_SINGLE_SCALAR);
 
        //stas des requetes qui sont encore en attente
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
     * Permet de modifier le nom d'un bracelet
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
        //envois de l'user au form pour avoir acces au nom de bracelet valide
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
     * Permet de modifier un porteur de bracelet
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
     * Permet de supprimer un porteur de bracelet
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
    * Affiche la liste des demande de liaison des bracelets du compte mother
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
     * Permet de modifier l'état d'une demande
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
     * Permet de supprimer une liaison de bracelet
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