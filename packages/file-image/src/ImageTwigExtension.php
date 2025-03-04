<?php

declare(strict_types=1);

/*
 * This file is part of rekalogika/file-src package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\File\Image;

use Psr\Container\ContainerInterface;
use Rekalogika\Contracts\File\FileInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ImageTwigExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    protected ContainerInterface $container;

    #[\Override]
    public static function getSubscribedServices(): array
    {
        return [
            ImageResizer::class,
        ];
    }

    #[Required]
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    private function getImageResizer(): ImageResizer
    {
        /** @var ImageResizer */
        $resizer = $this->container->get(ImageResizer::class);

        return $resizer;
    }

    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('image_resize', $this->fileImageResize(...)),
        ];
    }

    /**
     * @param ImageResizer::ASPECTRATIO_* $aspect
     */
    public function fileImageResize(
        ?FileInterface $file,
        int $maxWidthOrHeight = 512,
        string $aspect = ImageResizer::ASPECTRATIO_ORIGINAL,
    ): ?FileInterface {
        if ($file === null) {
            return null;
        }

        return $this->getImageResizer()
            ->take($file)
            ->resize($maxWidthOrHeight, $aspect)
            ->getResult();
    }
}
