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

namespace Rekalogika\Contracts\File;

use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Represents a container containing instances of FileInterface
 *
 * @extends \Traversable<array-key,FileInterface>
 */
interface FilesInterface extends
    \Traversable,
    \Countable
{
    /**
     * The name for this set of files, will be used as the name of the
     * downloaded folder or archive file, etc.
     */
    public function getName(): \Stringable&TranslatableInterface;
}
