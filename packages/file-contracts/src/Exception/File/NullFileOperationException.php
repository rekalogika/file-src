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

namespace Rekalogika\Contracts\File\Exception\File;

/**
 * A null file should not cause any side effect. Therefore, any operation
 * involving a null file that will potentially cause a side effect should throw
 * this exception.
 */
final class NullFileOperationException extends \RuntimeException implements FileException {}
