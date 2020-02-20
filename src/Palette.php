<?php declare(strict_types=1);

namespace SymfonyPaletteBundle;

use Throwable;
use Palette\Picture;
use Palette\Exception;
use Psr\Log\LoggerInterface;
use Palette\Generator\Server;
use Palette\SecurityException;
use Palette\Generator\IPictureLoader;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This file is part of the Nette Palette (https://github.com/MichaelPavlista/symfony-palette)
 * Copyright (c) 2019 Michael Pavlista (http://www.pavlista.cz/)
 * @author Michael Pavlista
 * @email  michael@pavlista.cz
 * @link   http://pavlista.cz/
 * @link   https://www.facebook.com/MichaelPavlista
 * @copyright 2019
 */
class Palette
{
    /** @var Server */
    protected $generator;

    /** @var string|null */
    protected $websiteUrl;

    /** @var bool is used relative urls for images? */
    protected $isUrlRelative;

    /** @var bool generator exceptions handling (FALSE = exceptions are thrown, TRUE = exceptions are begin detailed logged) */
    protected $handleExceptions = TRUE;

    /** @var string */
    protected $storageUrl;

    /** @var LoggerInterface */
    protected $logger;


    /**
     * Palette constructor.
     * @param LoggerInterface|null $logger
     * @param array $config
     * @param IPictureLoader|NULL $pictureLoader
     * @throws Exception
     */
    public function __construct
    (
        LoggerInterface $logger = NULL,
        array $config = [],
        IPictureLoader $pictureLoader = NULL
    )
    {
        // Setup image generator instance.
        $this->generator = new Server($config['path'], $config['url'], $config['basePath'], $config['signingKey']);

        // Set image storage url.
        $this->storageUrl = $config['url'];

        // Set error logger.
        $this->logger = $logger;

        // Register fallback image.
        if (TRUE === isset($config['fallbackImage']))
        {
            $this->generator->setFallbackImage($config['fallbackImage']);
        }

        // Register defined image query templates
        if (TRUE === isset($config['templates']) && is_array($config['templates']))
        {
            foreach ($config['templates'] as $templateName => $templateQuery)
            {
                $this->generator->setTemplateQuery($templateName, $templateQuery);
            }
        }

        if (TRUE === isset($config['defaultQuality']))
        {
            $this->generator->setDefaultQuality((int) $config['defaultQuality']);
        }

        // Set website url (optional)
        $this->websiteUrl = $config['websiteUrl'] ?? NULL;

        // Is used relative urls for images?
        $this->isUrlRelative =
            strpos($config['url'], '//') !== 0 &&
            strpos($config['url'], 'http://') !== 0 &&
            strpos($config['url'], 'https://') !== 0;

        // Set custom picture loader
        if ($pictureLoader)
        {
            $this->generator->setPictureLoader($pictureLoader);
        }
    }


    /**
     * Get image storage url.
     * @return string
     */
    public function getStorageUrl(): string
    {
        return $this->storageUrl;
    }


    /**
     * Set generator exceptions handling.
     * @param bool generator exceptions handling (FALSE = exceptions are thrown, TRUE = exceptions are begin detailed logged)
     * @throws Exception
     */
    public function setHandleExceptions($handleExceptions): void
    {
        if (is_bool($handleExceptions))
        {
            $this->handleExceptions = $handleExceptions;
        }
        else
        {
            throw new Exception('Invalid value for handleExceptions in configuration');
        }
    }


    /**
     * Get absolute url to image with specified image query string.
     * @param $image
     * @return null|string
     * @throws
     */
    public function __invoke(string $image): ?string
    {
        return $this->generator->loadPicture($image)->getUrl();
    }


    /**
     * Get url to image with specified image query string.
     * Supports absolute picture url when is relative generator url set.
     * @param string $image
     * @param string|null $imageQuery
     * @return null|string
     */
    public function getUrl(string $image, ?string $imageQuery = NULL): ?string
    {
        // Experimental support for absolute picture url when is relative generator url set
        if ($imageQuery && strpos($imageQuery, '//') === 0)
        {
            $imageQuery = mb_substr($imageQuery, 2);
            $imageUrl = $this->getPictureGeneratorUrl($image, $imageQuery);

            if ($this->isUrlRelative)
            {
                if ($this->websiteUrl)
                {
                    return $this->websiteUrl . $imageUrl;
                }

                return '//' . $_SERVER['SERVER_ADDR'] . $imageUrl;
            }

            return $imageUrl;
        }

        return $this->getPictureGeneratorUrl($image, $imageQuery);
    }


    /**
     * Get url to image with specified image query string from generator.
     * @param $image
     * @param null $imageQuery
     * @return null|string
     * @throws
     */
    protected function getPictureGeneratorUrl($image, $imageQuery = NULL): ?string
    {
        if ($imageQuery !== NULL)
        {
            $image .= '@' . $imageQuery;
        }

        return $this->generator->loadPicture($image)->getUrl();
    }


    /**
     * Get Palette picture instance.
     * @param $image
     * @return Picture
     * @throws
     */
    public function getPicture($image): Picture
    {
        return $this->generator->loadPicture($image);
    }


    /**
     * Get Palette generator instance.
     * @return Server
     */
    public function getGenerator(): Server
    {
        return $this->generator;
    }


    /**
     * Execute palette service generator backend.
     * @throws Throwable
     */
    public function serverResponse(): void
    {
        $requestImageQuery = '';

        try
        {
            // Get image query from url.
            $requestImageQuery = $this->generator->getRequestImageQuery();

            // Process server response.
            $this->generator->serverResponse();
        }
        catch (Throwable $exception)
        {
            // Handle server generating image response exception.
            if ($this->handleExceptions)
            {
                if ($exception instanceof SecurityException)
                {
                    $this->logger->error($exception->getMessage());

                    throw new BadRequestHttpException("Image doesn't exist");
                }

                $this->logger->error($exception->getMessage());
            }
            else
            {
                throw $exception;
            }

            // Return fallback image on exception if fallback image is configured.
            $fallbackImage = $this->generator->getFallbackImage();

            if ($fallbackImage)
            {
                $paletteQuery = preg_replace('/.*@(.*)/', $fallbackImage . '@$1', $requestImageQuery);

                $picture = $this->generator->loadPicture($paletteQuery);

                $savePath = $this->generator->getPath($picture);

                if (!file_exists($savePath))
                {
                    $picture->save($savePath);
                }

                $picture->output();
            }

            throw new BadRequestHttpException("Image doesn't exist");
        }
    }
}
