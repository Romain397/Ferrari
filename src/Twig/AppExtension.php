<?php

namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(private readonly Packages $packages)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('ferrari_star', [$this, 'ferrariStar']),
            new TwigFilter('optimized_image_url', [$this, 'optimizedImageUrl']),
        ];
    }

    public function ferrariStar(string $text, bool $highlight = false): string
    {
        if ($highlight) {
            return $text . ' ⭐';
        }

        return $text;
    }

    public function optimizedImageUrl(?string $url, int $width = 640): string
    {
        $source = trim((string) $url);
        if ($source === '') {
            return '';
        }

        // Rewrite Wikimedia originals/thumbnails to a smaller thumbnail size.
        if (str_contains($source, 'upload.wikimedia.org/wikipedia/commons/')) {
            $parts = parse_url($source);
            $path = (string) ($parts['path'] ?? '');
            if ($path === '') {
                return $source;
            }

            if (preg_match('#^/wikipedia/commons/thumb/(.+)/\d+px-(.+)$#', $path, $matches) === 1) {
                return 'https://upload.wikimedia.org/wikipedia/commons/thumb/' . $matches[1] . '/' . $width . 'px-' . $matches[2];
            }

            if (preg_match('#^/wikipedia/commons/(.+)/([^/]+)$#', $path, $matches) === 1) {
                return 'https://upload.wikimedia.org/wikipedia/commons/thumb/' . $matches[1] . '/' . $matches[2] . '/' . $width . 'px-' . $matches[2];
            }
        }

        if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {
            return $source;
        }

        return $this->packages->getUrl($source);
    }
}
