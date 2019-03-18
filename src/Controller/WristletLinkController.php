<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Requested;
use App\Entity\SerialNumber;
use App\Notification\Mailer;
use App\Form\SerialNumberType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
                           
                $numberBdd->setWristletTitle($form->get('wristletTitle')->getData());
                

                 /*selectionne le Role et le numero du bracelet pour lier a l'utilisateur*/
                 $repositoryRole = $this->getDoctrine()->getRepository(Role::class);
                 $roleAdded = $repositoryRole->findOneBytitle('ROLE_MOTHER');
                 $user = $this->getUser();
                 
                 $userMail = $user->getEmail();
                 
                 $roleAdded->addUser($user);
                 
                 $numberBdd->setActive(true);

                 $wristletAdded = $numberBdd;
                 $wristletAdded->addUserNumber($user);
                 $wristletAdded->setMailMother($userMail);
                 
                 $manager->persist($roleAdded);
                 $manager->persist($wristletAdded);
                 $manager->flush();

                 $this->addFlash(
                     'success',
                     "Compte lier, veuillez vous identifier a nouveau."
                 );
                 return $this->redirectToRoute('account_logged');
                                
            }
                
                 /*vérification si l'utilisateur n'a pas déjà une demande en attente*/
            if($requested == null && ($numberBdd->getMailMother() !== $user->getEmail())){
                 
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
                                $contactMother->sendMessage('noreply@wristband.com', $numberBdd->getMailMother(), 'Demande de liaison a un de vos bracelet', $bodyMail);
                                                        
                    $this->addFlash("warning", "Le bracelet a déjà été lier et nommé.Un mail de confirmation au compte principale a été envoyer.
                    Vous devez attrendre la confirmation de la personne détenteur du compte principale.");  

                }else if($numberBdd->getMailMother() == $user->getEmail()){
                    $this->addFlash("warning","dude t'est déja mother.");
                   
                }else if($requested[0]->getRequestedRefused() === true){
                    $this->addFlash("warning","dude t'es ban.");     

                }else if($requested !== null && $numberBdd->getMailMother() !== $user->getEmail()){
                    $this->addFlash("warning","dude t'a déja demander waiting.");
                
                } 
               
                
                
        };
            
        return $this->render('account/link.html.twig',[
        'form' => $form->createView()]);
    }
    


        /**
         *Vérification de l'intervale de temps entre un demande est la validation par mail
        */

        private function requestedInTime(\Datetime $RequestedAt = null)
        {
            if ($RequestedAt === null)
            {
                return false;        
            }
            
            $now = new \DateTime();
            $interval = $now->getTimestamp() - $RequestedAt->getTimestamp();
            $daySeconds = 60 * 10;//*24
            $response = $interval > $daySeconds ? false : $reponse = true;
            return $response;
        }
        /**
         * @Route("account/motherconfirmation/{id}/{token}", name="mother_validate")
         */
        public function motherValidate(Requested $requested, $token, ObjectManager $manager,Mailer $motherValidate)
        {
            // Récuperation de la bonne demande de liaison
            // Récupération dans le tableau du User,Numéro et du token pour les opérations qui suivront
            
            $requested = $this->getDoctrine()
            ->getRepository(Requested::class)
            ->findByrequestedToken($token);
            $user = $requested[0]->getRequestedBy();
            $userSerial = $requested[0]->getRequestedFor();
            $requestedAt = $requested[0]->getRequestedAt();
            $requestedToken = $requested[0]->getRequestedToken();
           
            // interdit l'accès à la page si:
            // le token associé au membre est null
            // le token enregistré en base et le token présent dans l'url ne sont pas égaux
            // le token date de plus de 10 minutes

            if ($requestedToken === null || $token !== $requestedToken || !$this->requestedInTime($requestedAt))
            {
                throw new AccessDeniedHttpException();
            }

                 $repositoryRole = $this->getDoctrine()->getRepository(Role::class);
                 $roleAdded = $repositoryRole->findOneBytitle('ROLE_CHILD');
                //Mise en relation du role et du numéro de série a lier
                 $roleAdded->addUser($user);
                 $userSerial->addUserNumber($user);
                                              
                 $manager->persist($roleAdded);
                 $manager->persist($userSerial);
                 //Suppression de la demande

                  $bodyMail = $motherValidate->createBodyMail('emails/mothervalidate.html.twig', [
                    'user' => $user,
                    'serialNumber' => $userSerial
                    ]
                ); 
                
                    $motherValidate->sendMessage('noreply@wristband.com', $user->getEmail(), 'Acceptation de la demande de liaison', $bodyMail);
                      
                 $manager->remove($requested[0]);
                 $manager->flush();

                 $this->addFlash(
                     'success',
                     "Compte lier."
                 );

                
                return $this->redirectToRoute('account_login');

                
                return $this->render('motherconfimation.html.twig', [
                    'form' => $form->createView()
                    ]);
        }

        /**
         * @Route("account/motherrefuse/{id}/{token}", name="mother_refuse")
         */
        public function motherRefuse($token, ObjectManager $manager,Mailer $motherRefuse)
        {
            $requested = $this->getDoctrine()
            ->getRepository(Requested::class)
            ->findByrequestedToken($token);
            $user = $requested[0]->getRequestedBy();
            $userSerial = $requested[0]->getRequestedFor();
            $requestedRefused = $requested[0]->setRequestedRefused(true);
            $requestedRefused = $requested[0]->setRequestedAt(Null);
            $requestedRefused = $requested[0]->setRequestedToken(Null);
            $manager->persist($requestedRefused);
            $manager->flush();
            $this->addFlash(
                'success',
                "Demande refusé."
            );

            $bodyMail = $motherRefuse->createBodyMail('emails/motherrefuse.html.twig', [
                'user' => $user,
                'serialNumber' => $userSerial
                ]
            ); 
            
                $motherRefuse->sendMessage('noreply@wristband.com', $user->getEmail(), 'Refus de la demande de liaison', $bodyMail);
               
           return $this->redirectToRoute('account_login');

        }
}