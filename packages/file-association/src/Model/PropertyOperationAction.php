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
enum PropertyOperationAction
{
    case Nothing;
    case Saved;
    case Removed;
    case LoadedNormal;
    case LoadedLazy;
    case LoadedMissing;
    case LoadedNotFound;

    public function toString(): string
    {
        return match ($this) {
            self::Nothing => 'Nothing',
            self::Saved => 'Saved',
            self::Removed => 'Removed',
            self::LoadedNormal => 'Loaded Normal',
            self::LoadedLazy => 'Loaded Lazy',
            self::LoadedMissing => 'Loaded Missing',
            self::LoadedNotFound => 'Loaded Not Found',
        };
    }
}
