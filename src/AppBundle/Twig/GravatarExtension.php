<?php

namespace AppBundle\Twig;


class GravatarExtension extends \Twig_Extension
{
    private $secure_request = true;

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('gravatar', array($this, 'gravatarFilter')),
        );
    }

    public function gravatarFilter($email, $size = null, $default = null)
    {
        $defaults = array(
            '404',
            'mm',
            'identicon',
            'monsterid',
            'wavatar',
            'retro',
            'blank'
        );
        $hash = md5($email);
        $url = $this->secure_request ? 'https://' : 'http://';
        $url .= 'www.gravatar.com/avatar/'.$hash;
        // Size
        if (!is_null($size)){
            $url .= "?s=$size";
        }
        // Default
        if (!is_null($default)){
            $url .= is_null($size) ? '?' : '&';
            $url .= 'd=' . (in_array($default, $defaults) ? $default : urlencode($default));
        }
        return $url;
    }

    public function getName()
    {
        return 'gravatar_extension';
    }
}