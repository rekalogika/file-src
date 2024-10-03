<?php

declare(strict_types=1);

use Rekalogika\File\Tests\TestKernel;

/*
 * This file is part of rekalogika/rekapager package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

require_once __DIR__ . '/../../vendor/autoload_runtime.php';

return fn(array $context): TestKernel => new TestKernel();
