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

namespace Rekalogika\File\Bridge\FilePond;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\TemporaryFile;

/**
 * Adapt a FilePond input using the File Encode plugin to a FileInterface
 *
 * @see https://pqina.nl/filepond/docs/api/plugins/file-encode/
 */
class FilePondFileEncodeAdapter
{
    /**
     * @param array<array-key,mixed> $input
     * @return FileInterface
     */
    public static function adaptFromArray(array $input): FileInterface
    {
        $data = $input['data'] ?? null;
        if (!is_string($data)) {
            throw new \UnexpectedValueException('Invalid FilePond input. Expecting "data" containing a Base64 string.');
        }

        $data = \base64_decode($data, true);
        if ($data === false) {
            throw new \UnexpectedValueException('Invalid Base64 string.');
        }

        $name = $input['name'] ?? null;
        if (!is_string($name)) {
            throw new \UnexpectedValueException('Invalid FilePond input. Expecting "name" containing a string.');
        }

        $type = $input['type'] ?? null;
        if (!is_string($type)) {
            throw new \UnexpectedValueException('Invalid FilePond input. Expecting "type" containing a string.');
        }

        $file = TemporaryFile::create('filepond-');

        $file->setContent($data);
        $file->setName($name);
        $file->setType($type);

        return $file;
    }

    public static function adaptFromString(string $jsonInput): FileInterface
    {
        $input = \json_decode($jsonInput, true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($input));

        return self::adaptFromArray($input);
    }
}
