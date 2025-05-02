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

interface ClassSignatureResolverInterface
{
    /**
     * Takes a class name and returns a string that uniquely identifies the
     * class. The string will be used as a part of the location of a file. The
     * result must consist of alphanumeric characters only. Because the location
     * of a file is potentially part of a public URL, the result should not
     * reveal internal information, like the class name itself.
     *
     * @param class-string $class
     */
    public function getClassSignature(string $class): ?string;
}
