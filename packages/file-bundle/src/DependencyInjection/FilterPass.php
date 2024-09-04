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

use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Derivation\Filter\FileFilterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FilterPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        $filters = $container
            ->findTaggedServiceIds('rekalogika.file.derivation.filter', true);

        $fileRepository = $container
            ->findDefinition(FileRepositoryInterface::class);

        foreach (array_keys($filters) as $id) {
            $definition = $container->findDefinition($id);

            $class = $definition->getClass();
            if ($class === null) {
                throw new \InvalidArgumentException(\sprintf('Service "%s" has no class', $id));
            }

            $reflection = $container->getReflectionClass($class);
            if ($reflection === null) {
                throw new \InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }

            if (!$reflection->isSubclassOf(FileFilterInterface::class)) {
                throw new \InvalidArgumentException(\sprintf('Service %s is not %s', $id, FileFilterInterface::class));
            }

            $definition
                ->addMethodCall('setFileRepository', [$fileRepository]);
        }
    }
}
