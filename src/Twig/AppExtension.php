<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('truncate', array($this, 'truncate')),
        );
    }
    public function truncate($string)
    {
        if (mb_strlen($string) >= 40){
            $string = mb_substr($string, 0, 37).'...';
        }
        return $string;
    }
}