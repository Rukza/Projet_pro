<?php
namespace App\Notification;

use App\Entity\Contact;
use Twig\Environment;

class MailLinkWristlet {

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
    /*
    to do send au bon destinataire
    */

    public function notifyMother($bypassSpool = false){
        $message = (new \Swift_Message('Mail de confirmation du compte pour le site wristband.com'))
        ->setFrom('noreply@sitewristband.com')
                ->setTo('rukza@orange.fr')
                ->setReplyTo('noreply@sitewristband.com')
                ->setBody($this->renderer->render('emails/motherconfirmation.html.twig',[
                    
                ]), 'text/html');
               
                $this->mailer->send($message);

                if($bypassSpool) {
                    $spool = $this->mailer->getTransport->getSpool();
                    $spool->flushQueue(new Swift_SmtpTransport(
                       
                    ));
                }
    }
}





