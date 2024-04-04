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

namespace Rekalogika\File\Bridge\FilePond\DependencyInjection;

use Rekalogika\File\Bridge\FilePond\FilePondType;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class RekalogikaFileFilePondExtension extends Extension implements
    PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container
            ->setDefinition(FilePondType::class, new Definition(FilePondType::class))
            ->addTag('form.type');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig', [
            'form_themes' => ['@RekalogikaFileFilePond/filepond_form_theme.html.twig']
        ]);

        if ($this->isAssetMapperAvailable($container)) {
            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__ . '/../../assets/dist' => '@rekalogika/file-filepond',
                    ],
                ],
            ]);
        }
    }


    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        // check that FrameworkBundle 6.3 or higher is installed
        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');

        if (!\is_array($bundlesMetadata)) {
            return false;
        }

        $frameworkBundleMetadata = $bundlesMetadata['FrameworkBundle'] ?? null;

        if (!\is_array($frameworkBundleMetadata)) {
            return false;
        }

        $path = $frameworkBundleMetadata['path'] ?? null;

        if (!\is_string($path)) {
            return false;
        }

        return is_file($path . '/Resources/config/asset_mapper.php');
    }
}
