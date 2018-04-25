<?php

namespace AppBundle\EventListener;

use AppBundle\Event\PostObservationEvent;
use AppBundle\Service\Notificator;

class PostObservationListener
{
    protected $notificator;

    public function __construct(Notificator $notificator)
    {
        $this->notificator = $notificator;
    }

    public function notify(PostObservationEvent $event) {
        $contenu = "Espèce : " . $event->getObservation()->getBird()->getNomCourant() . "<br>";
        $contenu .= "Nombre d'individus : " . $event->getObservation()->getIndividuals() . "<br>";
        $contenu .= "Adresse : " . $event->getObservation()->getAdresse() . "<br>";
        $contenu .= "Latitude : " . $event->getObservation()->getLatitude() . " Longitude : " . $event->getObservation()->getLongitude() . "<br>";
        $contenu .= "Observation : " . $event->getObservation()->getCommentaire();

        $datas = array(
            'sujet'     => 'Nouvelle observation à valider',
            'nom'       => $event->getObservation()->getUser()->getUsername(),
            'contenu'   => $contenu,
            'email'     => $event->getObservation()->getUser()->getEmail()
        );
        $this->notificator->notifyByEmail($datas, $event->getBccNaturalistes());
    }

}