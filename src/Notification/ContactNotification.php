<?php
namespace App\Notification;

use App\Entity\Contact;
use Twig\Environment;

class ContactNotification {

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Environment
     */
    private $renderer;


    public function __construct(\Swift_Mailer $mailer, Environment $renderer){

        $this->mailer = $mailer;
        $this->renderer = $renderer;

    }


    public function notify(Contact $contact,  $bypassSpool = false){
        $message = (new \Swift_Message('Notification :' .$contact->getSujet()))
                ->setFrom($contact->getEmail())
                ->setTo('society.kis@gmail.com')
                ->setReplyTo($contact->getEmail())
                ->setBody($this->renderer->render('emails/contact.html.twig',[
                    'contact' => $contact
                ]), 'text/html');
               
                $this->mailer->send($message);

                if($bypassSpool) {
                    $spool = $this->mailer->getTransport->getSpool();
                    $spool->flushQueue(new Swift_SmtpTransport(
                       
                    ));
                }
    }
}





