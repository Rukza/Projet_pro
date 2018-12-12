<?php

namespace App\Notification;

use App\Entity\User;
use Twig\Environment;


class MailPswdReset
{
    private $engine;
    private $mailer;

    public function __construct(\Swift_Mailer $mailer, Environment $engine)
    {
        $this->engine = $engine;
        $this->mailer = $mailer;
    }

    public function sendMessage($from, $to, $subject, $body, $attachement = null)
    {
        $mail = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo($from)
            ->setContentType('text/html');

        $this->mailer->send($mail);
    }

    public function createBodyMail($view, array $parameters)
    {
        return $this->engine->render($view, $parameters);
    }
}