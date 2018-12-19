<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use App\Notification\MailNotification;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class AccountController extends AbstractController
{
    /**
     * Permet l'affichage et le formulaire de connexion'
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
                'hasError' => $error !== null,
                'username' => $username
        ]);
        
    }
    /**
     * Permet gérer la déconnection 
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout(){
            //
    }

  /**
     * Permet d'afficher le formulaire d'insciption
     * 
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, MailNotification $valitation ){
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()){

        $hash = $encoder->encodePassword($user, $user->getPswd());
        $user->setPswd($hash);

        $manager->persist($user);
        $manager->flush();

        $valitation->confirmation($user);
        $this->addFlash(
            'success',
            "votre compte a bien été crée!, un email vas vous être envoyé"

            
        );
        return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

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
     * @IsGranted("ROLE_USER", statusCode=404, message="No access! Get out!")
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

    public function link(){
        return $this->render('account/link.html.twig');
    }
     /**
     *Permet d'afficher les données de fréquance cardiaque d'un bracelet
     *
     * @Route("/account/cardio", name="account_cardio")
     * @Security("is_granted('ROLE_MOTHER') or is_granted('ROLE_CHILD')")
     * @return Response
     */

    public function cardio(){
        return $this->render('account/cardio.html.twig');
    }
}
