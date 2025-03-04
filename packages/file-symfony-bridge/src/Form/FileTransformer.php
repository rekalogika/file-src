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

namespace Rekalogika\File\Bridge\Symfony\Form;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Bridge\Symfony\HttpFoundation\ToHttpFoundationFileAdapter;
use Rekalogika\File\FileAdapter;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Transforms a HttpFoundation File to FileInterface and back
 *
 * @implements DataTransformerInterface<FileInterface,File>
 */
final class FileTransformer implements DataTransformerInterface
{
    /**
     * Transforms a FileInterface to HttpFoundation File
     *
     * @return ($value is null ? null : File)
     */
    #[\Override]
    public function transform($value): ?File
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof File) {
            return $value;
        }

        // @phpstan-ignore instanceof.alwaysTrue
        if (!$value instanceof FileInterface) {
            throw new TransformationFailedException(\sprintf(
                'Expecting "%s", but getting "%s" instead.',
                FileInterface::class,
                get_debug_type($value),
            ));
        }

        return ToHttpFoundationFileAdapter::adapt($value);
    }

    /**
     * Transform a HttpFoundation File to a FileInterface
     *
     * @param ?File $value
     * @return ($value is null ? null : FileInterface)
     */
    #[\Override]
    public function reverseTransform(mixed $value): ?FileInterface
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof FileInterface) {
            return $value;
        }

        // @phpstan-ignore instanceof.alwaysTrue
        if (!$value instanceof File) {
            throw new TransformationFailedException(\sprintf(
                'Expecting "%s", but getting "%s" instead.',
                File::class,
                get_debug_type($value),
            ));
        }

        return FileAdapter::adapt($value);
    }
}
