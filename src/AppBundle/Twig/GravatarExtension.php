<?php

namespace AppBundle\Twig;


class GravatarExtension extends \Twig_Extension
{
    private $secure_request = false;
    public function getFilters()
    {
        return array(
            'gravatar' => new \Twig_Filter_Method($this, 'gravatarFilter'),
            'sgravatar' => new \Twig_Filter_Method($this, 'secureGravatarFilter'),
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
    public function secureGravatarFilter($email, $size = null, $default = null)
    {
        $this->secure_request = true;
    }
    public function getName()
    {
        return 'gravatar_extension';
    }
}