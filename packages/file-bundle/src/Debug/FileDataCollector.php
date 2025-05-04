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

    public function collectObjectOperationResult(
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
    public function getObjectOperationResults(): array
    {
        /** @var list<ObjectOperationResult> */
        return $this->data['object_operation_results'] ?? [];
    }

    public function getCount(): int
    {
        return \count($this->getObjectOperationResults());
    }

    #[\Override]
    public function reset(): void
    {
        $this->data = [];
        parent::reset();
    }
}
