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

namespace Rekalogika\File\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder<'array'>
     */
    #[\Override]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('rekalogika_file');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('filesystems')
                    ->info('Filesystems registered in FileRepository in a key-value pairs. The key is the identifier of the filesystem, and the value is the filesystem instance.')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('identifier')
                    ->scalarPrototype()->end()
                ->end()

                ->scalarNode('default_filesystem_directory')
                    ->info('The storage directory used by the default filesystem.')
                    ->defaultValue('%kernel.project_dir%/var/storage/default')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
