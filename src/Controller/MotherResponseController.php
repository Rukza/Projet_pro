<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Requested;
use App\Form\Mother\Response\RequestedType;
use App\Notification\Mailer;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MotherResponseController extends AbstractController
{
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
     *Suite au mail reçus par le compte principale 
     *Affichage du formulaire pour Accepter,Refuser,Bannir une demande de liaison
     *
     * @Route("account/motherresponse/{id}/{token}", name="mother_response")
     * 
     * @return Response
     */
    
    public function RequestMother(Request $request, $token,ObjectManager $manager,Mailer $motherResponse)
    {

        // Récuperation de la bonne demande de liaison
        
        $requested = $this->getDoctrine()
        ->getRepository(Requested::class)
        ->findByrequestedToken($token);
        
        $form = $this->createForm(RequestedType::class);
        $form->handleRequest($request);
        //$requestedToken = $requested[0]->getRequestedToken();
   
        
        // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 10 minutes
        /*if($requestedToken === null || $token !== $requestedToken || !$this->requestedInTime($requestedAt))
        {
        throw new AccessDeniedHttpException();
        }else{*/

            //Si le btn refuser est choisie
            if($form->get('refuser')->isClicked() && $form->isValid())
            {
    
                $user = $requested[0]->setRequestedRefused(true);
                $messageMother = $requested[0]->setRequestedMotherResponse($form->get('requestedMotherResponse')->getData());
                $user = $requested[0]->getRequestedBy();
                $userSerial= $requested[0]->getRequestedFor();
                $manager->persist($user);
                $manager->persist($messageMother);
                $manager->flush();

                $bodyMail = $motherResponse->createBodyMail('emails/motherrefuse.html.twig', [
                    'user' => $user,
                    'serialNumber' => $userSerial,
                    'messageMother' => $messageMother
                    ]
                ); 
                
                $motherResponse->sendMessage('noreply@wristband.com', $user->getEmail(), 'Refus de la liaison', $bodyMail);
                    
                $this->addFlash(
                'success',
                "La demande de {$user->getEmail()} a bien été refusée, il pourra cependant vous refaire une demande pour le bracelet {$userSerial->getWristletTitle()}. Si toute fois vous veniez à changer d'avis, connecter vous à votre administration et accepter ou bannisser la demande"
                );
            
            }
            //Si le btn accepter est choisie
            if ($form->get('accepter')->isClicked() )
            {
                $repositoryRole = $this->getDoctrine()->getRepository(Role::class);
                $roleAdded = $repositoryRole->findOneBytitle('ROLE_CHILD');
                
                $user = $requested[0]->setRequestedAccepted(true);
                $user = $requested[0]->getRequestedBy();
                 $userSerial = $requested[0]->getRequestedFor();
                //Mise en relation du role et du numéro de série a lier                
                $roleAdded->addUser($user);
                    
                    
                                                
                $manager->persist($roleAdded);
                $manager->persist($user);
                $manager->flush();
            
                $bodyMail = $motherResponse->createBodyMail('emails/mothervalidate.html.twig', [
                'user' => $user,
                'serialNumber' => $userSerial
                ]
                ); 
                
                $motherResponse->sendMessage('noreply@wristband.com', $user->getEmail(), 'Acceptation de la demande de liaison', $bodyMail);
                        
                //$manager->remove($requested[0]);
                    
            
                $this->addFlash(
                'success',
                "La demande de {$user->getEmail()} a bien été accepté, il pourra donc consulter les données du bracelet {$userSerial->getWristletTitle()}. Si toute fois vous veniez à changer d'avis, connecter vous à votre administration et supprimer ou bannisser la demande"
                );
            }
        
            //Si le btn bannir est choisie
            if ($form->get('bannir')->isClicked())
            {
                $user = $requested[0]->getRequestedBy();
                $userSerial= $requested[0]->getRequestedFor();
                $requestedBan = $requested[0]->setRequestedBanned(true);
                $requestedBan = $requested[0]->setRequestedAt(Null);
                $requestedBan = $requested[0]->setRequestedToken(Null);
                $manager->persist($requestedBan);
                $manager->flush();
                
                $this->addFlash(
                    'success',
                    "La demande de {$user->getEmail()} a bien été refusée et il ne pourra plus vous faire de demande pour le bracelet {$userSerial->getWristletTitle()}. Si toute fois vous veniez a changer d'avis vous pourrez supprimer cela en vous connectant a votre page d'administration de bracelet"
                );
        
                $bodyMail = $motherResponse->createBodyMail('emails/motherban.html.twig', [
                    'user' => $user,
                    'serialNumber' => $userSerial
                    ]
                ); 
        
                $motherResponse->sendMessage('noreply@wristband.com', $user->getEmail(), 'Refus de la demande de liaison et bannissement', $bodyMail);
            }
    
            return $this->render('account/motherresponse.html.twig',[
            'requested' => $requested[0],
            'form' => $form->createView()
            ]);
        }
    
    
}