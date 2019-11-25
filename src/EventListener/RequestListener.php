<?php declare(strict_types=1);

namespace SymfonyPaletteBundle\EventListener;

use Throwable;
use SymfonyPaletteBundle\Palette;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RequestListener
 * @package SymfonyPaletteBundle\EventListener
 */
class RequestListener
{
    /** @var Palette */
    private $palette;


    /**
     * RequestListener constructor.
     * @param Palette $palette
     */
    public function __construct(Palette $palette)
    {
        $this->palette = $palette;
    }


    /**
     * onKernelRequest process pallete generator backend.
     * @param GetResponseEvent $getResponseEvent
     * @throws Throwable
     */
    public function onKernelRequest(GetResponseEvent $getResponseEvent): void
    {
        if (strpos($getResponseEvent->getRequest()->getUri(), $this->palette->getStorageUrl()) === 0)
        {
            $this->palette->serverResponse();

            die();
        }
    }
}
