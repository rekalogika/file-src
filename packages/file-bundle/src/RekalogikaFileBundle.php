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

use Rekalogika\File\Bundle\DependencyInjection\FilterPass;
use Rekalogika\File\Derivation\Filter\FileFilterInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class RekalogikaFileBundle extends Bundle
{
    #[\Override]
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    #[\Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        if (interface_exists(FileFilterInterface::class)) {
            $container->addCompilerPass(new FilterPass());
        }
    }
}
