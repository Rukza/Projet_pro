<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Notification\ContactNotification;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * 
     * @return Response
     */
    public function index(Request $resquest, ContactNotification $notification)
    {
        
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($resquest);
        

        if ($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash(
                'success',
                'Votre email a bien été envoyé'
            );
            return $this->redirectToRoute('homepage');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
