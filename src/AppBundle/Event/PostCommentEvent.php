<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PostCommentEvent extends Event
{
    protected $content;
    protected $pseudo;

    public function __construct($content, $pseudo)
    {
        $this->content = $content;
        $this->pseudo = $pseudo;
    }

    public function getContent() {
        return $this->content;
    }

    public function getPseudo() {
        return $this->pseudo;
    }
}