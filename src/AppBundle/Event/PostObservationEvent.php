<?php

namespace AppBundle\Event;

use AppBundle\Entity\Observation;
use Symfony\Component\EventDispatcher\Event;

class PostObservationEvent extends Event
{
    protected $observation;
    protected $bccNaturalistes;

    public function __construct(Observation $observation, $bccNaturalistes)
    {
        $this->observation = $observation;
        $this->bccNaturalistes = $bccNaturalistes;
    }

    public function getObservation() {
        return $this->observation;
    }

    public function getBccNaturalistes() {
        return $this->bccNaturalistes;
    }
}