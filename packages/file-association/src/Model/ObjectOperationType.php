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

namespace Rekalogika\File\Association\Model;

/**
 * @internal
 */
enum ObjectOperationType
{
    case Flush;

    case Load;

    case Remove;

    public function getString(): string
    {
        return match ($this) {
            self::Flush => 'Flush',
            self::Load => 'Load',
            self::Remove => 'Remove',
        };
    }
}
