<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Mailer extends Controller
{
    protected $twig;
    protected $mailer;
    protected $mailer_user;
    protected $mailer_password;
    protected $mailer_host;
    protected $mailer_port;
    protected $mailer_from;
    protected $mailer_to;

    public function __construct(\Twig_Environment $twig_Environment, \Swift_Mailer $mailer, $mailer_user, $mailer_password, $mailer_host, $mailer_port, $mailer_from, $mailer_to)
    {
        $this->twig = $twig_Environment;
        $this->mailer = $mailer;
        $this->mailer_user = $mailer_user;
        $this->mailer_password = $mailer_password;
        $this->mailer_host = $mailer_host;
        $this->mailer_port = $mailer_port;
        $this->mailer_from = $mailer_from;
        $this->mailer_to = $mailer_to;
    }

    public function sendEmail($data)
    {
        $transport = \Swift_SmtpTransport::newInstance($this->mailer_host, $this->mailer_port,'tls')
            ->setUsername($this->mailer_user)
            ->setPassword($this->mailer_password)
        ;

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = (new \Swift_Message('[NAO] '.$data["sujet"].' - depuis nao.com'))
            ->setFrom($this->mailer_from, $data["email"])
            ->setTo($this->mailer_to)
            ->setBody($this->twig->render('nao/contact/email.html.twig', array(
                'data' => $data
            )))
        ;

        $message->setContentType("text/html");

        return $mailer->send($message);
    }
}