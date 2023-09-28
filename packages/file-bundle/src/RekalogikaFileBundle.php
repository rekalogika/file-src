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

namespace Rekalogika\File\Bundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Rekalogika\Domain\File\Association\Entity\EmbeddedMetadata;
use Rekalogika\File\Bundle\DependencyInjection\FilterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RekalogikaFileBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // filter
        $container->addCompilerPass(new FilterPass());

        // doctrine
        if (
            class_exists(EmbeddedMetadata::class)
            && class_exists(DoctrineOrmMappingsPass::class)
        ) {
            $path = (new \ReflectionClass(EmbeddedMetadata::class))
                ->getFileName();

            if (empty($path)) {
                throw new \RuntimeException('Unable to get path of EmbeddedMetadata class');
            }

            $configDir = realpath(dirname(dirname($path)) . 'config/doctrine');
            if (false === $configDir) {
                throw new \RuntimeException('Unable to get path of EmbeddedMetadata class');
            }

            $mappings = [
                $configDir => 'Rekalogika\Domain\File\Association\Entity',
            ];

            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createXmlMappingDriver($mappings)
            );
        }
    }
}
