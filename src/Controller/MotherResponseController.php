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

/*
  Controller features for a user who they have ROLE_MOTHER 
  to select what he want to do with the resquest who they have recive by mail notification :

  - Refuse (user mother can use the form to send more detail at the resqueter user)
  - Accept (allow to requester user to see heart beat from the wristlet managed by the mother account)
  - Refuse and block (refuse and block the user resquester for this wristlet)
*/



class MotherResponseController extends AbstractController
{
    
     /**
     * Display the features for a user who they have ROLE_MOTHER to select 
     * what he want to do with the resquest who they have recive by mail notification
     *
     * @Route("account/motherresponse/{id}/{token}", name="mother_response")
     * 
     * @return Response
     */
    
    public function RequestMother(Request $request, $token,ObjectManager $manager,Mailer $motherResponse)
    {
        
        $requested = $this->getDoctrine()
        ->getRepository(Requested::class)
        ->findByrequestedToken($token);
        
        $form = $this->createForm(RequestedType::class);
        $form->handleRequest($request);
        
   
        
        //TODO Allow or Deny request in time if they have pass the time accepted
        //See if the requested token is valide
       
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
                "La demande de {$user->getEmail()} a bien été refusée, il pourra cependant vous refaire une demande pour le bracelet {$userSerial->getWristletTitle()}. Si toute fois vous veniez à changer d'avis, connectez-vous à votre administration et acceptez ou bloquez la demande"
                );
            
            }
            
            if ($form->get('accepter')->isClicked() )
            {
                $repositoryRole = $this->getDoctrine()->getRepository(Role::class);
                $roleAdded = $repositoryRole->findOneBytitle('ROLE_CHILD');
                
                $user = $requested[0]->setRequestedAccepted(true);
                $user = $requested[0]->getRequestedBy();
                $userSerial = $requested[0]->getRequestedFor();
                
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
                
                $this->addFlash(
                'success',
                "La demande de {$user->getEmail()} a bien été acceptée, il pourra donc consulter les données du bracelet {$userSerial->getWristletTitle()}. Si toutefois vous veniez à changer d'avis, connectez vous à votre administration et supprimez ou bloquez la demande"
                );
            }
        
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
                    "La demande de {$user->getEmail()} a bien été refusée et il ne pourra plus vous faire de demande pour le bracelet {$userSerial->getWristletTitle()}. Si toutefois vous veniez à changer d'avis vous pourrez supprimer cela en vous connectant a votre page d'administration de bracelet"
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