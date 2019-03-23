<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Requested;
use App\Entity\SerialNumber;
use App\Notification\Mailer;
use App\Form\SerialNumberType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;



class WristletLinkController extends AbstractController
{


    /**
     *Permet d'afficher le formulaire pour lier un bracelet au compte
     *
     * @Route("/account/link", name="account_link")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function link(Request $request, ObjectManager $manager, Mailer $contactMother, TokenGeneratorInterface $tokenGenerator){

    
        $form = $this->createForm(SerialNumberType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            /*recuperation des données entré dans le form et de l'utilisateur qui la soumis*/
                $repository = $this->getDoctrine()->getRepository(SerialNumber::class);
                $numberBdd = $repository->findOneByserialWristlet($form->get('serialWristlet')->getData());
                

                $user = $this->getUser();

                $requested = $this->getDoctrine()
                            ->getRepository(Requested::class)
                            ->findBy(array('requestedBy' => $user, 'requestedFor' => $numberBdd));
                            
           /*Vérification si le bracelet demandé n'a pas déjà été validé*/
            if($numberBdd->getActive() === null){ 
                           
                 /*selectionne le Role et le numero du bracelet pour lier a l'utilisateur*/
                 $repositoryRole = $this->getDoctrine()->getRepository(Role::class);
                 $roleAdded = $repositoryRole->findOneBytitle('ROLE_MOTHER');
                 $user = $this->getUser();
                 $user->addMotherFor($numberBdd);                 
                 
                 $roleAdded->addUser($user);
                 $numberBdd->setActive(true);
                 
                 //$numberBdd->setMother($user);
                        
                 $manager->persist($roleAdded);
                 $manager->persist($numberBdd);
                 $manager->flush();

                 $this->addFlash(
                     'success',
                     "Compte lier, afin que les modifications soit prise en compte veuillez vous déconnecter et vous connecter a nouveau."
                 );
                 
                 return $this->redirectToRoute('account_login');
                                
            }
            $numberBdd->getMother()->getEmail();
            
                 /*vérification si l'utilisateur n'a pas déjà une demande en attente*/
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
                                                        
                    $this->addFlash("warning", "Le bracelet a déjà été lier et nommé.Un mail de confirmation au compte principale a été envoyer.
                    Vous devez attrendre la confirmation de la personne détenteur du compte principale.");  
                      
                }else if($numberBdd->getMother() == $user = $this->getUser()){
                    $this->addFlash("warning","dude t'est déja mother.");
                   
                }else if($requested[0]->getRequestedBanned() === true){
                    $this->addFlash("warning","dude t'es ban.");     

                }else if($requested !== null && $numberBdd->getMother() !== $user = $this->getUser()){
                    $this->addFlash("warning","dude t'a déja demander waiting.");
                
                } 
               
                
                
        };
            
        return $this->render('account/link.html.twig',[
        'form' => $form->createView()]);
    }
    


       
        /**
         * @Route("/admingen", name="GenSerial") 
         * @IsGranted("ROLE_ADMIN")
         */


        //Generate cryptographied number to do in admin controller

        function serialRand(ObjectManager $manager, $longueur = 15, $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
        {
            $newNumber = new SerialNumber;
            $chaine = '';
            $max = mb_strlen($listeCar, '8bit') - 1;
            for ($i = 0; $i < $longueur; ++$i) {
                $chaine .= $listeCar[random_int(0, $max)];
            }
            
            $newNumber->setSerialWristlet($chaine);
                         
            $manager->persist($newNumber);
            $manager->flush();
            $this->addFlash(
                'success',
                "Un nouveau numéro de serie a bien été crée {$newNumber->getSerialWristlet()}"
            );
            return $this->redirectToRoute('account_logged');
        }

}