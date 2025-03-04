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

namespace Rekalogika\File\Adapter;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\File;

/**
 * Adapter to convert an \SplFileInfo to a FileInterface object.
 */
final class FromSplFileInfoAdapter extends File
{
    private function __construct(
        private readonly \SplFileInfo $source,
    ) {
        parent::__construct($this->source->getRealPath());
    }

    public static function adapt(\SplFileInfo $source): FileInterface
    {
        return new self($source);
    }

    public function getWrapped(): \SplFileInfo
    {
        return $this->source;
    }
}
