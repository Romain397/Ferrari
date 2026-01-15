<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('ferrari_star', [$this, 'ferrariStar']),
        ];
    }

    public function ferrariStar(string $text, bool $highlight = false): string
    {
        if ($highlight) {
            return $text . ' ⭐';
        }

        return $text;
    }
}
