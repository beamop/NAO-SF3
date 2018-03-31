<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Mailer extends Controller
{
    private $twig;

    private $mailer;

    private $mailer_user;

    private $mailer_password;

    public function __construct(\Twig_Environment $twig_Environment, \Swift_Mailer $mailer, $mailer_user, $mailer_password)
    {
        $this->twig = $twig_Environment;
        $this->mailer = $mailer;
        $this->mailer_user = $mailer_user;
        $this->mailer_password = $mailer_password;
    }

    public function sendEmail($data)
    {
        $transport = \Swift_SmtpTransport::newInstance('smtp.mailtrap.io', 2525,'tls')
            ->setUsername($this->mailer_user)
            ->setPassword($this->mailer_password)
        ;

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = (new \Swift_Message('[NAO] '.$data["sujet"].' - depuis nao.com'))
            ->setFrom('noreply@nao.com', $data["email"])
            ->setTo('noreply@nao.com')
            ->setBody($this->twig->render('nao/contact/template.html.twig', array(
                'data' => $data
            )))
        ;

        $message->setContentType("text/html");

        return $mailer->send($message);
    }
}