<?php

namespace AppBundle\EventListener;

use AppBundle\Event\PostCommentEvent;
use AppBundle\Service\Notificator;

class PostCommentListener
{
    protected $notificator;

    public function __construct(Notificator $notificator)
    {
        $this->notificator = $notificator;
    }

    public function notify(PostCommentEvent $event) {
        $datas = array(
            'sujet'     => 'Nouveau commentaire',
            'nom'       => $event->getPseudo(),
            'contenu'   => $event->getContent(),
            'email'     => null
        );
        $this->notificator->notifyByEmail($datas);
    }

}