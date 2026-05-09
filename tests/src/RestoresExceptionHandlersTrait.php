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

namespace Rekalogika\File\Tests;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;

trait RestoresExceptionHandlersTrait
{
    private int $exceptionHandlerStackDepth = 0;
    private int $errorHandlerStackDepth = 0;

    #[Before]
    protected function recordHandlerStackDepth(): void
    {
        $this->exceptionHandlerStackDepth = self::countExceptionHandlers();
        $this->errorHandlerStackDepth = self::countErrorHandlers();
    }

    #[After]
    protected function restoreExceptionAndErrorHandlers(): void
    {
        while (self::countExceptionHandlers() > $this->exceptionHandlerStackDepth) {
            restore_exception_handler();
        }

        while (self::countErrorHandlers() > $this->errorHandlerStackDepth) {
            restore_error_handler();
        }
    }

    private static function countExceptionHandlers(): int
    {
        $count = 0;
        $stack = [];

        while (true) {
            $handler = set_exception_handler(static fn() => null);
            restore_exception_handler();
            if ($handler === null) {
                break;
            }
            $stack[] = $handler;
            restore_exception_handler();
            $count++;
        }

        foreach (array_reverse($stack) as $handler) {
            set_exception_handler($handler);
        }

        return $count;
    }

    private static function countErrorHandlers(): int
    {
        $count = 0;
        $stack = [];

        while (true) {
            $handler = set_error_handler(static fn(): bool => false);
            restore_error_handler();
            if ($handler === null) {
                break;
            }
            $stack[] = $handler;
            restore_error_handler();
            $count++;
        }

        foreach (array_reverse($stack) as $handler) {
            set_error_handler($handler);
        }

        return $count;
    }
}
