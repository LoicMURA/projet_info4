<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice($number, $currency = '€'): string
    {
        $price = number_format($number, 2, ',', '.');
        $price = $currency === '€' ? $price.'€' : '$'.$price;

        return $price;
    }
}