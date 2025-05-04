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

namespace Rekalogika\File\Bundle\Debug;

use Rekalogika\File\Association\Contracts\ObjectManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

final readonly class TraceableObjectManager implements ObjectManagerInterface
{
    public function __construct(
        private ObjectManagerInterface $decorated,
        private Stopwatch $stopwatch,
    ) {}

    #[\Override]
    public function flushObject(object $object): void
    {
        $this->stopwatch->start('file.object_manager.flush');
        $this->decorated->flushObject($object);
        $this->stopwatch->stop('file.object_manager.flush');
    }

    #[\Override]
    public function removeObject(object $object): void
    {
        $this->stopwatch->start('file.object_manager.remove');
        $this->decorated->removeObject($object);
        $this->stopwatch->stop('file.object_manager.remove');
    }

    #[\Override]
    public function loadObject(object $object): void
    {
        $this->stopwatch->start('file.object_manager.load');
        $this->decorated->loadObject($object);
        $this->stopwatch->stop('file.object_manager.load');
    }
}
