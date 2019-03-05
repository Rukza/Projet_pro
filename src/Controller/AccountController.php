<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\AccountType;
use App\Entity\SerialNumber;
use App\Entity\PasswordUpdate;
use App\Form\SerialNumberType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use App\Notification\MailLinkWristlet;
use App\Notification\MailNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;




class AccountController extends AbstractController
{

    /**
     *Permet d'afficher les fonctionnalitées reservé au profil inscrit
     *
     * @Route("/account/logged", name="account_logged")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function account(){
        return $this->render('account/logged.html.twig');
    }
    
    /**
     *Permet d'afficher et de modifier les données du compte
     *
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function profile(Request $request, ObjectManager $manager){
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()){

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre email a bien été modifié'
            );
            return $this->redirectToRoute('account_logged');
        }

        
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     *Permet de modifier le mot de passe
     * @IsGranted("ROLE_USER")
     * @Route("/account/updatepswd", name="account_updatepswd")
     * 
     * @return Response
     */

    public function updatepswd(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager){
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPswd())){

                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel!"));
                } else {

                    $newPassword = $passwordUpdate->getNewPassword();
                    $pswd = $encoder->encodePassword($user, $newPassword);

                    $user->setPswd($pswd);

                    $manager->persist($user);
                    $manager->flush();

                    $this->addFlash(
                        'success',
                        "Votre mot de passe a bien été modifié"
                    );

                    return $this->redirectToRoute('homepage');

                }
                

            }
        
        return $this->render('account/updatepswd.html.twig',[
            'form' => $form->createView()
        ]);
    }
    
    
    
    /**
     *Permet d'afficher et de lier un bracelet au compte
     *
     * @Route("/account/link", name="account_link")
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function link(Request $request,ObjectManager $manager, MailLinkWristlet $contactMother){

    
        $form = $this->createForm(SerialNumberType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            
            $repository = $this->getDoctrine()->getRepository(SerialNumber::class);
            $numberBdd = $repository->findOneByserialWristlet($form->get('serialWristlet')->getData());
            $numberBdd->setWristletTitle($form->get('wristletTitle')->getData());
            
            if ($numberBdd->getActive() === !null )
            {
                $contactMother->notifyMother();   
                $this->addFlash("warning", "Le bracelet a déjà été lier et nomé.Un mail de confirmation au compte principale a été envoyer.
                Vous devez attrendre la confirmation de la personne détenteur du compte principale.");
                    
                
            }else{

           
                /* selectionne le Role et le numero du bracelet pour lier a l'utilisateur*/
            $repositoryRole = $this->getDoctrine()->getRepository(Role::class);
            $roleAdded = $repositoryRole->findOneBytitle('ROLE_MOTHER');
            $wristletAdded = $numberBdd;
            $user = $this->getUser();
           
            $wristletAdded->addUserNumber($user);
            
            
                       
            $roleAdded->addUser($user);
            
            $manager->flush();
            $manager->persist($roleAdded);
            $manager->persist($wristletAdded);

            $this->addFlash(
                'success',
                "Compte lier, veuillez vous identifier a nouveau."
            );
           
            return $this->redirectToRoute('account_login');
        }
        }
        
        return $this->render('account/link.html.twig',[
            'form' => $form->createView()]);
    }
     /**
     *Permet d'afficher les données de fréquance cardiaque d'un bracelet
     *
     * @Route("/account/cardiolist", name="account_cardio")
     * @Security("is_granted('ROLE_MOTHER') or is_granted('ROLE_CHILD')")
     * @return Response
     */

    public function cardio(){
        return $this->render('account/cardiolist.html.twig');
    }
}
