<?php

namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly Packages $packages,
        private readonly KernelInterface $kernel
    )
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

        $relativePath = ltrim($source, '/');
        if (str_starts_with($relativePath, 'uploads/')) {
            $optimizedPath = $this->buildLocalOptimizedImage($relativePath, $width);
            if ($optimizedPath !== null) {
                return $this->packages->getUrl($optimizedPath);
            }
        }

        return $this->packages->getUrl($source);
    }

    private function buildLocalOptimizedImage(string $relativePath, int $width): ?string
    {
        if ($width <= 0 || !extension_loaded('gd')) {
            return null;
        }

        $sourceAbsolutePath = $this->kernel->getProjectDir() . '/public/' . $relativePath;
        if (!is_file($sourceAbsolutePath)) {
            return null;
        }

        $extension = strtolower((string) pathinfo($sourceAbsolutePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return null;
        }

        $hash = substr(sha1($relativePath), 0, 12);
        $basename = pathinfo($sourceAbsolutePath, PATHINFO_FILENAME);
        $optimizedRelativePath = sprintf('uploads/cache/%d/%s-%s.webp', $width, $basename, $hash);
        $optimizedAbsolutePath = $this->kernel->getProjectDir() . '/public/' . $optimizedRelativePath;

        if (is_file($optimizedAbsolutePath)) {
            return $optimizedRelativePath;
        }

        if (!$this->generateWebpThumbnail($sourceAbsolutePath, $optimizedAbsolutePath, $width)) {
            return null;
        }

        return $optimizedRelativePath;
    }

    private function generateWebpThumbnail(string $sourceAbsolutePath, string $targetAbsolutePath, int $targetWidth): bool
    {
        $imageInfo = @getimagesize($sourceAbsolutePath);
        if ($imageInfo === false) {
            return false;
        }

        [$sourceWidth, $sourceHeight] = $imageInfo;
        if ($sourceWidth <= 0 || $sourceHeight <= 0) {
            return false;
        }

        $mimeType = (string) ($imageInfo['mime'] ?? '');
        $sourceImage = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($sourceAbsolutePath),
            'image/png' => @imagecreatefrompng($sourceAbsolutePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourceAbsolutePath) : false,
            'image/gif' => @imagecreatefromgif($sourceAbsolutePath),
            default => false,
        };

        if (!$sourceImage) {
            return false;
        }

        $ratio = $sourceHeight / $sourceWidth;
        $newWidth = min($targetWidth, $sourceWidth);
        $newHeight = max(1, (int) round($newWidth * $ratio));

        $targetImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($targetImage === false) {
            imagedestroy($sourceImage);
            return false;
        }

        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        $targetDirectory = dirname($targetAbsolutePath);
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0775, true) && !is_dir($targetDirectory)) {
            imagedestroy($sourceImage);
            imagedestroy($targetImage);
            return false;
        }

        $result = @imagewebp($targetImage, $targetAbsolutePath, 80);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return (bool) $result;
    }
}
