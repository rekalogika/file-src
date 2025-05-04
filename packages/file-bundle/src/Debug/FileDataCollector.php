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

use Rekalogika\File\Association\Model\ObjectOperationResult;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @internal
 */
final class FileDataCollector extends AbstractDataCollector implements ResetInterface
{
    #[\Override]
    public function getName(): string
    {
        return 'rekalogika_file';
    }

    #[\Override]
    public static function getTemplate(): string
    {
        return "@RekalogikaFile/data_collector.html.twig";
    }

    #[\Override]
    public function collect(
        Request $request,
        Response $response,
        ?\Throwable $exception = null,
    ): void {}

    public function collectResult(
        ObjectOperationResult $objectOperationResult,
    ): void {
        /**
         * @psalm-suppress MixedArrayAssignment
         * @phpstan-ignore offsetAccess.nonOffsetAccessible
         */
        $this->data['object_operation_results'][] = $objectOperationResult;
    }

    /**
     * @return list<ObjectOperationResult>
     */
    public function getResults(): array
    {
        /** @var list<ObjectOperationResult> */
        return $this->data['object_operation_results'] ?? [];
    }

    public function getTotalTime(): float
    {
        $totalTime = 0.0;

        foreach ($this->getResults() as $result) {
            $totalTime += $result->duration ?? 0.0;
        }

        return $totalTime;
    }

    public function getObjectCount(): int
    {
        return \count($this->getResults());
    }

    public function getPropertyCount(): int
    {
        $count = 0;

        foreach ($this->getResults() as $result) {
            $count += \count($result->propertyResults);
        }

        return $count;
    }

    #[\Override]
    public function reset(): void
    {
        $this->data = [];
        parent::reset();
    }
}
