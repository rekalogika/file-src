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
use Rekalogika\File\Association\Model\ObjectOperationResult;
use Symfony\Component\Stopwatch\Stopwatch;

final readonly class TraceableObjectManager implements ObjectManagerInterface
{
    public function __construct(
        private ObjectManagerInterface $decorated,
        private Stopwatch $stopwatch,
        private FileDataCollector $dataCollector,
    ) {}

    private function getRandom(): string
    {
        return substr(hash('xxh128', uniqid((string) mt_rand(), true)), 0, 6);
    }

    #[\Override]
    public function flushObject(object $object): ObjectOperationResult
    {
        $stopwatchId = 'file.object.flush-' . $this->getRandom();

        $this->stopwatch->start($stopwatchId);
        $result = $this->decorated->flushObject($object);
        $end = $this->stopwatch->stop($stopwatchId);

        $result = $result->withDuration($end->getDuration());

        $this->dataCollector->collectObjectOperationResult($result);

        return $result;
    }

    #[\Override]
    public function removeObject(object $object): ObjectOperationResult
    {
        $stopwatchId = 'file.object.remove-' . $this->getRandom();

        $this->stopwatch->start($stopwatchId);
        $result = $this->decorated->removeObject($object);
        $end = $this->stopwatch->stop($stopwatchId);

        $result = $result->withDuration($end->getDuration());

        $this->dataCollector->collectObjectOperationResult($result);

        return $result;
    }

    #[\Override]
    public function loadObject(object $object): ObjectOperationResult
    {
        $stopwatchId = 'file.object.load-' . $this->getRandom();

        $this->stopwatch->start($stopwatchId);
        $result = $this->decorated->loadObject($object);
        $end = $this->stopwatch->stop($stopwatchId);

        $result = $result->withDuration($end->getDuration());

        $this->dataCollector->collectObjectOperationResult($result);

        return $result;
    }
}
