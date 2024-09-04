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

namespace Rekalogika\File;

use Rekalogika\Contracts\File\Exception\File\LocalTemporaryFileException;

/**
 * Represents a local temporary file
 */
final class LocalTemporaryFile extends \SplFileInfo
{
    private function __construct(string $file)
    {
        parent::__construct($file);
    }

    /**
     * An instance of LocalTemporaryFile needs to be prevented from falling out
     * of scope. This is because the destructor will delete the file. Often
     * callers will just use an \SplFileInfo instance as if it is a string of
     * the path, which might cause the instance to fall out of scope, and
     * __destructed. We prevent that from happening by throwing an exception
     * here.
     */
    #[\Override]
    public function __toString(): string
    {
        trigger_deprecation('rekalogika/file', 'latest', 'LocalTemporaryFile should not be cast to string. Use getPathname() instead.');
        return parent::__toString();

        // disable this for now
        // throw new \LogicException('LocalTemporaryFile cannot be cast to string. Use getPathname() instead.');
    }

    public function __destruct()
    {
        if (file_exists($this->getPathname())) {
            unlink($this->getPathname());
        }
    }

    final public static function create(?string $prefix = null): self
    {
        $prefix ??= 'localtemporaryfile-';
        $path = tempnam(sys_get_temp_dir(), $prefix);

        if ($path === false) {
            throw new LocalTemporaryFileException(\sprintf(
                'Cannot create a temporary file with prefix "%s" in system temporary directory.',
                $prefix,
            ));
        }

        return new self($path);
    }

    /**
     * Wrap an existing file as a temporary file. Takes mixed instead of string
     * to prevent automatic casting to string
     */
    final public static function createFromExisting(
        mixed $file,
    ): self {
        if (!\is_string($file)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'File must be a string, "%s" given',
                    get_debug_type($file),
                ),
            );
        }

        if (!file_exists($file)) {
            throw new LocalTemporaryFileException(
                \sprintf('File "%s" does not exist', $file),
            );
        }

        return new self($file);
    }

    final public static function createFromString(
        string $content,
        ?string $prefix = null,
    ): self {
        $file = self::create($prefix);
        file_put_contents($file->getPathname(), $content);

        return $file;
    }
}
