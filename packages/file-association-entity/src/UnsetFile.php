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

namespace Rekalogika\Domain\File\Association\Entity;

use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\FileTypeInterface;
use Rekalogika\Contracts\File\NullFileInterface;
use Rekalogika\Domain\File\Null\NullFileTrait;
use Rekalogika\Domain\File\Null\NullName;
use Rekalogika\Domain\File\Null\NullType;

/**
 * A null file that indicates the file is unset. Used by `FileTrait` when the
 * file property is unset.
 */
class UnsetFile extends \Exception implements NullFileInterface
{
    use NullFileTrait;

    /**
     * @param class-string $className
     */
    public function __construct(string $className, string $propertyName)
    {
        parent::__construct(sprintf(
            'File property "%s" in class "%s" is unset. This might be caused by the use of `AbstractQuery::toIterable()`. If that is the case, you can: 1. stop involving "%s" in the query; 2. pre-hydrate the file entities before the query; or 3. use other means to iterate the query.',
            $propertyName,
            $className,
            $className
        ));
    }

    public function getFilesystemIdentifier(): ?string
    {
        return null;
    }

    public function getKey(): string
    {
        return '/dev/null';
    }

    public function getName(): FileNameInterface
    {
        return new NullName('(unset)', 'rekalogika_file');
    }

    public function getType(): FileTypeInterface
    {
        return new NullType('The file is unset due to an error', 'rekalogika_file');
    }

    private function throwException(string $message): never
    {
        throw $this;
    }
}
