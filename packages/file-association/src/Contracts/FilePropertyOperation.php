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

namespace Rekalogika\File\Association\Contracts;

/**
 * @internal
 */
enum FilePropertyOperation
{
    /**
     * No action taken in flush operation
     */
    case FlushNothing;

    /**
     * File is saved as the result of a flush operation
     */
    case FlushSave;

    /**
     * File is removed as the result of a flush operation
     */
    case FlushRemove;

    /**
     * File is loaded normally as the result of a load operation
     */
    case LoadNormal;

    /**
     * File is not found in a load operation, a MissingFile is substituted
     * for the file
     */
    case LoadMissing;

    /**
     * A lazy load proxy is used for the file in a load operation
     */
    case LoadLazy;

    /**
     * File is not found in a load operation, a null value is used
     */
    case LoadNull;

    /**
     * File is removed as the result of a remove operation
     */
    case Remove;

    public function getString(): string
    {
        return match ($this) {
            self::FlushNothing => 'Flush->Nothing',
            self::FlushSave => 'Flush->Save',
            self::FlushRemove => 'Flush->Remove',
            self::LoadNormal => 'Load->Normal',
            self::LoadMissing => 'Load->Missing',
            self::LoadLazy => 'Load->Lazy',
            self::LoadNull => 'Load->Null',
            self::Remove => 'Remove',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::FlushNothing => 'No action taken in flush operation',
            self::FlushSave => 'File is saved as the result of a flush operation',
            self::FlushRemove => 'File is removed as the result of a flush operation',
            self::LoadNormal => 'File is loaded normally as the result of a load operation',
            self::LoadMissing => 'File is not found in a load operation, a MissingFile is substituted for the file',
            self::LoadLazy => 'A lazy load proxy is used for the file in a load operation',
            self::LoadNull => 'File is not found in a load operation, a null value is used',
            self::Remove => 'File is removed as the result of a remove operation',
        };
    }
}
