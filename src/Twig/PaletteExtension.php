<?php declare(strict_types=1);

namespace SymfonyPaletteBundle\Twig;

use Twig\TwigFilter;
use SymfonyPaletteBundle\Palette;
use Twig\Extension\AbstractExtension;

/**
 * Class PaletteExtension
 * @package SymfonyPaletteBundle\Twig
 */
class PaletteExtension extends AbstractExtension
{
    /** @var Palette */
    private $palette;


    /**
     * PaletteExtension constructor.
     * @param Palette $palette
     */
    public function __construct(Palette $palette)
    {
        $this->palette = $palette;
    }


    /**
     * Returns extension filters.
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('palette', [$this, 'palette']),
        ];
    }


    /**
     * Returns url for modified image.
     * @param string $picture
     * @param string|null $query
     * @return string|null
     */
    public function palette(string $picture, ?string $query = NULL): ?string
    {
        return $this->palette->getUrl($picture, $query);
    }
}
