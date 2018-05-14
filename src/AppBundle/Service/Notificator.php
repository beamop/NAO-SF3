<?php

namespace AppBundle\Service;


class Notificator
{
    protected $twig;
    protected $mailer;
    protected $mailTo;
    protected $mailFrom;

    public function __construct(\Twig_Environment $twig_Environment, \Swift_Mailer $mailer, $mailTo, $mailFrom)
    {
        $this->twig = $twig_Environment;
        $this->mailer = $mailer;
        $this->mailTo = $mailTo;
        $this->mailFrom = $mailFrom;
    }

    // Méthode pour notifier par e-mail un administrateur
    public function notifyByEmail($datas, $setTo = null, $setBcc = null)
    {
        if ($setTo) {
            $mailTo = $setTo;
        } else {
            $mailTo = $this->mailTo;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject("[NAO] ".$datas['sujet'])
            ->setFrom($this->mailFrom)
            ->setTo($mailTo)
            ->setBody($this->twig->render('nao/email/notification.html.twig', array(
                'datas' => $datas
            )))
            ->setContentType("text/html")
        ;

        if ($setBcc) {
            $message->setBcc($setBcc);
        }

        $this->mailer->send($message);
    }
}