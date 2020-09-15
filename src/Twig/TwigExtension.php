<?php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isLoggedIn', function () {
                $logged_in = false;
                if (!empty($_COOKIE['token'])) {
                    $logged_in = true;
                }
                return $logged_in;
            })
        ];
    }
}
