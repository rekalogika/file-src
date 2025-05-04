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

use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Model\PropertyMetadata;
use Rekalogika\File\Association\Model\PropertyOperationResult;
use Symfony\Component\Stopwatch\Stopwatch;

final readonly class TraceableFilePropertyManager implements FilePropertyManagerInterface
{
    public function __construct(
        private FilePropertyManagerInterface $decorated,
        private Stopwatch $stopwatch,
    ) {}

    private function getRandom(): string
    {
        return substr(hash('xxh128', uniqid((string) mt_rand(), true)), 0, 6);
    }

    #[\Override]
    public function flushProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult {
        $stopwatchId = 'file.property.flush-' . $this->getRandom();

        $this->stopwatch->start($stopwatchId);
        $result = $this->decorated->flushProperty($propertyMetadata, $object, $id);
        $end = $this->stopwatch->stop($stopwatchId);

        $result = $result->withDuration($end->getDuration());

        return $result;
    }

    #[\Override]
    public function removeProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult {
        $stopwatchId = 'file.property.remove-' . $this->getRandom();

        $this->stopwatch->start($stopwatchId);
        $result = $this->decorated->removeProperty($propertyMetadata, $object, $id);
        $end = $this->stopwatch->stop($stopwatchId);

        $result = $result->withDuration($end->getDuration());

        return $result;
    }

    #[\Override]
    public function loadProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult {
        $stopwatchId = 'file.property.load-' . $this->getRandom();

        $this->stopwatch->start($stopwatchId);
        $result = $this->decorated->loadProperty($propertyMetadata, $object, $id);
        $end = $this->stopwatch->stop($stopwatchId);

        $result = $result->withDuration($end->getDuration());

        return $result;
    }
}
