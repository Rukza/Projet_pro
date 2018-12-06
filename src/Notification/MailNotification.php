<?php
namespace App\Notification;

use App\Entity\User;
use Twig\Environment;

class MailNotification {

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


    public function confirmation(User $user,  $bypassSpool = false){
        $message = (new \Swift_Message('Mail de confirmation du compte pour le site wristband.com'))
                ->setFrom('noreply@sitewristband.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderer->render('emails/confirmation.html.twig',[
                    'user' => $user
                ]), 'text/html');
               
                $this->mailer->send($message);

                if($bypassSpool) {
                    $spool = $this->mailer->getTransport->getSpool();
                    $spool->flushQueue(new Swift_SmtpTransport(
                       
                    ));
                }
    }
}





