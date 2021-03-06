<?php

namespace App\Controller;

use App\Form\Account\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\Account\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/*
  Controller for the features for register and connected user

  - features for the account management
  - form to edit the user account data
  - form to modify the account password
*/




class AccountController extends AbstractController
{

     /**
     * Displays the features for the account management
     *
     * @Route("/account/profile/logged", name="account_logged")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */

    public function account(){
        
        return $this->render('account/profile/logged.html.twig');
    }
    
    /**
     * Display the form to edit the user account data : - email
     *                                             - adress
     *                                             - postal code
     *                                             - city
     *
     * @Route("/account/profile/updateprofile", name="account_profile")
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

        
        return $this->render('account/profile/updateprofile.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
     /**
     * Display the form to modify the account password
     * 
     * 
     * @IsGranted("ROLE_USER")
     * @Route("/account/profile/updatepswd", name="account_updatepswd")
     * 
     * @return Response
     */

    public function updatepswd(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager){
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // vérify if the old password required in the form, was the actual password of the user
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
        
        return $this->render('account/profile/updatepswd.html.twig',[
            'form' => $form->createView()
        ]);
    }
};
