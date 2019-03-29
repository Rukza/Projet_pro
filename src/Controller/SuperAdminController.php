<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Weared;
use App\Entity\Requested;
use App\Entity\SerialNumber;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\WearedRepository;
use App\Form\Admin\Users\UserAddType;
use App\Form\Admin\Users\UserEditType;
use App\Repository\RequestedRepository;
use App\Form\Admin\Weared\WearedAddType;
use App\Form\Mother\Weared\WearedEditType;
use App\Repository\SerialNumberRepository;
use App\Form\Admin\Requested\RequestAddType;
use App\Form\Admin\Requested\RequestEditType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Admin\SerialNumber\SerialNumberEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SuperAdminController extends AbstractController
{
    /**
     * @Route("/admin/index", name="super_admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(ObjectManager $manager)
    {
        $users = $manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
        $serials = $manager->createQuery('SELECT COUNT(s) FROM App\Entity\SerialNumber s')->getSingleScalarResult();
        $requests = $manager->createQuery('SELECT COUNT(r) FROM App\Entity\Requested r')->getSingleScalarResult();
        $wears = $manager->createQuery('SELECT COUNT(w) FROM App\Entity\Weared w')->getSingleScalarResult();


        return $this->render('/admin/index.html.twig',[
            'stats' => compact('users','serials', 'requests', 'wears')
        ]);
    }


    /**
    * @Route("/admin/users/usermanagement", name="users_management")
    * @IsGranted("ROLE_ADMIN")
    * @return Response
    */
    public function usersManagement(UserRepository $userRepo){
        
        return $this->render('admin/users/usermanagement.html.twig',[
            'users' =>$userRepo->findAll()
        ]);
    }
    /**
     * Permet d'ajouter un utilisateur
     * 
     * @Route("/admin/users/add", name="admin_user_add")
     * @IsGranted("ROLE_ADMIN")
     * @return response 
     */

    public function addUser(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        $user = new User();
        $form = $this->createForm(UserAddType::class,$user);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                $hash = $encoder->encodePassword($user, $user->getPswd());
                $user->setPswd($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "l'utilisateur {$user->getFullName()} a bien ajouté !"
                );
            }
            return $this->render('admin/users/add.html.twig',[
                'users' => $user,
                'form' => $form->createView()
            ]);
    }
    
     /**
     * Permet de modifier un utilisateur
     * 
     * @Route("/admin/users/{id}/edit", name="admin_user_edit")
     * @IsGranted("ROLE_ADMIN")
     * @return response 
     */

    public function editUser(User $user, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
       
        $form = $this->createForm(UserEditType::class,$user);
        
        $oldRole = $user->getUserRoles();
        $oldRole[0]->removeUser($user);    
        $role = $form->get('userRoles')->getData();
        
        $form->handleRequest($request);
      
        if($form->isSubmitted() && $form->isValid()){
            $user->getUserRoles();
            
                $hash = $encoder->encodePassword($user, $user->getPswd());
                $user->setPswd($hash);
                $role[0]->removeUser($user);
                $role[0]->addUser($user);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "l'utilisateur {$user->getFullName()} a bien été modifié !"
                );
            }
            return $this->render('admin/users/edit.html.twig',[
                'users' => $user,
                'form' => $form->createView()
            ]);
    }

     /**
     * Permet de supprimer un utilisateur
     *
     * @Route ("/admin/users/usermanagement/{id}/delete", name="admin_user_delete")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function deleteUser(User $users, ObjectManager $manager){
            $manager->remove($users);
            $manager->flush();
            
            $this->addflash(
                'success',
                "L'utilisateur a bien été supprimée !"
                
            );
            return $this->redirectToRoute('users_management');
        }




    /**
    * @Route("/admin/wristlets/wristletmanagement", name="wristlets_management")
    * @IsGranted("ROLE_ADMIN")
    * @return Response
    */
    public function serialsManagement(SerialNumberRepository $serialRepo){
        
        return $this->render('admin/wristlets/wristletmanagement.html.twig',[
            'wristlets' =>$serialRepo->findAll()
        ]);
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
            $newNumber->setAttributedTo(false);
            $newNumber->setWristletTitle("Undefine");
            $user = $this->getUser();
            $newNumber->setMother($user);
            $manager->persist($newNumber);
            $manager->flush();
            $this->addFlash(
                'success',
                "Un nouveau numéro de serie a bien été crée {$newNumber->getSerialWristlet()}");
            return $this->redirectToRoute('wristlets_management');
    }
    /**
     * Permet de modifier un bracelet
     *
     * @Route ("/admin/wristlets/{id}/edit", name="admin_wristlet_edit")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function editWristlet(SerialNumber $serials, ObjectManager $manager, Request $request){
        $form = $this->createForm(SerialNumberEditType::class,$serials);
        

        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
                $manager->persist($serials);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "le bracelet a bien été modifié !"
                );
            }
            return $this->render('admin/wristlets/edit.html.twig',[
                'serials' => $serials,
                'form' => $form->createView()
            ]);
    }

    /**
     * Permet de supprimer un bracelet
     *
     * @Route ("/admin/wristlets/wristletmanagement/{id}/delete", name="admin_wristlet_delete")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function deleteWristlet(SerialNumber $serials, ObjectManager $manager){
        $manager->remove($serials);
        $manager->flush();
        
        $this->addflash(
            'success',
            "Le bracelet a bien été supprimée !"
            
        );
        return $this->redirectToRoute('wristlets_management');
    }



    /**
    * @Route("/admin/weareds/wearedmanagement", name="weared_management")
    * @IsGranted("ROLE_ADMIN")
    * @return Response
    */
    public function wearedManagement(WearedRepository $wearedRepo){
        
        return $this->render('admin/weareds/wearedmanagement.html.twig',[
            'weareds' =>$wearedRepo->findAll()
        ]);
    }

    /**
     * Permet d'ajouter un porteur
     * 
     * @Route("/admin/weareds/add", name="admin_weared_add")
     * @IsGranted("ROLE_ADMIN")
     * @return response 
     */

    public function addWeared(Request $request, ObjectManager $manager){
        $wearer = new Weared();
        $form = $this->createForm(WearedAddType::class,$wearer);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
               
                $manager->persist($wearer);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "le porteur de bracelet {$wearer->getFullName()} a bien ajouté !"
                );
            }
            return $this->render('admin/weareds/add.html.twig',[
                'wears' => $wearer,
                'form' => $form->createView()
            ]);
    }
    
     /**
     * Permet de modifier un porteur
     * 
     * @Route("/admin/weareds/{id}/edit", name="admin_weared_edit")
     * @IsGranted("ROLE_ADMIN")
     * @return response 
     */

    public function editweared(Weared $wear, Request $request, ObjectManager $manager){
       
        $form = $this->createForm(WearedEditType::class,$wear);
        
        
        $form->handleRequest($request);
      
        if($form->isSubmitted() && $form->isValid()){
           
                $manager->persist($wear);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Le porteur de bracelet {$wear->getFullName()} a bien été modifié !"
                );
            }
            return $this->render('admin/weareds/edit.html.twig',[
                'wears' => $wear,
                'form' => $form->createView()
            ]);
    }

     /**
     * Permet de supprimer un porteur
     *
     * @Route ("/admin/users/weareds/{id}/delete", name="admin_weared_delete")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function deleteweared(Weared $wear, ObjectManager $manager){
            $mother = $wear->getWearWristlet();
            $mother->setAttributedTo(false);
            $manager->remove($wear);
            $manager->flush();
            
            $this->addflash(
                'success',
                "Le porteur de bracelet a bien été supprimée !"
                
            );
            return $this->redirectToRoute('weared_management');
        }

    
      /**
    * @Route("/admin/requests/requestwristletmanagement", name="requests_management")
    * @IsGranted("ROLE_ADMIN")
    * @return Response
    */
    public function requetsManagement(RequestedRepository $requestsRepo){
        
        return $this->render('admin/requests/requestwristletmanagement.html.twig',[
            'requests' =>$requestsRepo->findAll()
        ]);
    }

    /**
     * Permet d'ajouter un utilisateur
     * 
     * @Route("/admin/requests/add", name="admin_requests_add")
     * @IsGranted("ROLE_ADMIN")
     * @return response 
     */

    public function addRequests(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        $requested = new Requested();
        $form = $this->createForm(RequestAddType::class,$requested);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                $manager->persist($requested);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "La demande de {$requested->getRequestedBy()->getFullName()} pour {$requested->getRequestedFor()} a bien été ajouté !"
                );
            }
            return $this->render('admin/requests/add.html.twig',[
                'requests' => $requested,
                'form' => $form->createView()
            ]);
    }
    /**
     * Permet de modifier une demande ou une laison enfant
     *
     * @Route ("/admin/requests/{id}/edit", name="admin_requests_edit")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function editRequests(Requested $requested, ObjectManager $manager, Request $request){
        $form = $this->createForm(RequestEditType::class,$requested);
        

        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
                $manager->persist($requested);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "La demande de {$requested->getRequestedBy()->getFullName()} pour {$requested->getRequestedFor()} a bien été modfiée !"
                );
            }
            return $this->render('admin/requests/edit.html.twig',[
                'requests' => $requested,
                'form' => $form->createView()
            ]);
    }






    /**
     * Permet de supprimer une demande ou une liaison enfant
     *
     * @Route ("/admin/requests/requestwristletmanagement/{id}/delete", name="admin_requests_delete")
     * @return Response
     */
    public function deleteRequested(Requested $requested, ObjectManager $manager){
        $manager->remove($requested);
        $manager->flush();
        
        $this->addflash(
            'success',
            "La demande de {$requested->getRequestedBy()->getFullName()} pour {$requested->getRequestedFor()} a bien été supprimée !"
            
        );
        return $this->redirectToRoute('requests_management');
    }


   
}
