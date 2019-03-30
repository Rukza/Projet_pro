<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Requested;
use App\Entity\SerialNumber;
use App\Notification\Mailer;
use App\Form\WristletLink\SerialNumberType;
use App\Form\WristletLink\SerialNumberRenameType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;



class WristletLinkController extends AbstractController
{

     /**
     * Display the form to link a wristlet to an user if they have the right serial code
     *
     * @Route("/account/link/link", name="account_link")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function link(Request $request, ObjectManager $manager, Mailer $contactMother, TokenGeneratorInterface $tokenGenerator){

    
        $form = $this->createForm(SerialNumberType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

                $repository = $this->getDoctrine()->getRepository(SerialNumber::class);
                $numberBdd = $repository->findOneByserialWristlet($form->get('serialWristlet')->getData());
                

                $user = $this->getUser();

                $requested = $this->getDoctrine()
                            ->getRepository(Requested::class)
                            ->findBy(array('requestedBy' => $user, 'requestedFor' => $numberBdd));
                            
           // Vérify if the wristlet is alredy active if not the user can be the mother account
            if($numberBdd->getActiveSerial() === null){ 
                           
                 
                 $repositoryRole = $this->getDoctrine()->getRepository(Role::class);
                 $roleAdded = $repositoryRole->findOneBytitle('ROLE_MOTHER');
                 $user = $this->getUser();
                 $user->addMotherFor($numberBdd);                 
                 
                 $roleAdded->addUser($user);
                 $numberBdd->setActiveSerial(true);
                 
                 $numberBdd->setMother($user);
                      
                 $manager->persist($roleAdded);
                 $manager->persist($numberBdd);
                 $manager->flush();
                 
                 $this->addFlash(
                     'success',
                     "Compte lier, veuillez nommer le bracelet que vous venez de lier."
                 );
                 
                 return $this->redirectToRoute('mother_wristlet_named',array(
                     'id'=>$numberBdd->getId()
                 ));
                                
            }
            $numberBdd->getMother()->getEmail();
            
                // Vérify if the request exist and if the wristlet is not already link to a mother account
            if($requested == null && ($numberBdd->getMother() !== $user = $this->getUser())){
               
                    $requested = new Requested();

                    $requested->setRequestedToken($tokenGenerator->generateToken());

                    $requested->setRequestedAt(new \Datetime());

                    $requested->setRequestedFor($numberBdd);

                    $requested->setRequestedBy($user);
                                            
                    $manager->persist($requested);
                    $manager->flush();
                    
                    $bodyMail = $contactMother->createBodyMail('emails/motherconfirmation.html.twig', [
                                'requested' => $requested,
                                'user' => $user,
                                'serialNumber' => $numberBdd
                                ]
                            );
                            
                                $contactMother->sendMessage('noreply@wristband.com', $numberBdd->getMother()->getEmail(), 'Demande de liaison a un de vos bracelet', $bodyMail);
                                                        
                    $this->addFlash("warning", "Le bracelet a déjà été lier et nommé.Un mail de confirmation au compte principale a été envoyé.
                    Vous devez attrendre la confirmation de la personne détenteur du compte principale.");  
                      
                }else if($numberBdd->getMother() == $user = $this->getUser()){
                    $this->addFlash("warning","Vous ne pouvez refaire une demande sur ce bracelet car vous avez déjà lier a ce compte.");
                   
                }else if($requested[0]->getRequestedBanned() === true){
                    $this->addFlash("warning","Vous ne pouvez pas faire une demande pour ce bracelet, votre compte a été bloquer par la personne détentrice des droits de ce bracelet.");     

                }else if($requested !== null && $numberBdd->getMother() !== $user = $this->getUser()){
                    $this->addFlash("warning","Vous avez déjà fait une demande de liaison sur ce bracelet, veuillez attendre que la personne détentruce des droits valide la demande.");
                
                } 
       
        };
            
        return $this->render('account/link/link.html.twig',[
        'form' => $form->createView()]);
    }   


    /**
     * Display the form where the user mother must named his wristlet
     *
     * @Route ("/account/link/namewristlet/{id}", name="mother_wristlet_named")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function nameWristlet($id,SerialNumber $serials, ObjectManager $manager, Request $request){

        $form = $this->createForm(SerialNumberRenameType::class,$serials);
        
        $repository = $this->getDoctrine()->getRepository(SerialNumber::class);
        $numberBdd = $repository->findOneByid($id);
                
        
        $form->handleRequest($request);
        if($numberBdd->getMother() == $user = $this->getUser()){

            if($form->isSubmitted()&& $form->isValid()){
                $manager->persist($serials);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "le bracelet a bien été nomé, afin que les modifications soit prise en compte veuillez vous déconnecter et vous connecter à nouveau."
                );
            }
        }else{
            $this->addFlash(
                'warning',
                "Il semblerait que le bracelet que vous tentez de renomer ne vous appartient pas!, merci de le laisser tranquille"
            );
            return $this->redirectToRoute('homepage');
        }
            return $this->render('/account/link/namewristlet.html.twig',[
                'serials' => $serials,
                'form' => $form->createView()
            ]);
    }
}