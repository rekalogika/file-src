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

namespace Rekalogika\File\MetadataSerializer;

use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\File\RawMetadata;

class MetadataSerializer implements MetadataSerializerInterface
{
    #[\Override]
    public function serialize(RawMetadataInterface $metadata): string
    {
        $array = iterator_to_array($metadata);

        return json_encode($array, \JSON_FORCE_OBJECT | \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }

    #[\Override]
    public function deserialize(string $serialized): RawMetadataInterface
    {
        $array = json_decode($serialized, true, 512, \JSON_THROW_ON_ERROR);

        if (!\is_array($array)) {
            return new RawMetadata();
        }

        /** @var array<string,int|string|bool|null> $array */
        $array = $this->convertLegacyData($array);

        return new RawMetadata($array);
    }

    /**
     * @param array<array-key,mixed> $inputArray
     * @return array<string,int|string|bool|null>
     */
    private function convertLegacyData(array $inputArray): array
    {
        $legacyMetadata = $inputArray['metadata'] ?? null;
        unset($inputArray['metadata']);

        $newArray = [];
        foreach ($inputArray as $key => $value) {
            if (\is_string($key) && (\is_string($value) || \is_bool($value) || \is_int($value) || \is_null($value))) {
                $newArray[$key] = $value;
            }
        }

        if (!\is_array($legacyMetadata)) {
            return $newArray;
        }

        /** @psalm-suppress MixedAssignment */
        foreach ($legacyMetadata as $key => $value) {
            if (!\is_string($value) && !\is_null($value) && !\is_int($value)) {
                continue;
            }

            switch ($key) {
                case 'disposition':
                    $newArray[Constants::HTTP_DISPOSITION] = (string) $value;
                    break;

                case 'file-name':
                    $file = $value;
                    if (!\is_string($file) && !\is_null($file)) {
                        $file = null;
                    }

                    $newArray[Constants::FILE_NAME] = $file;
                    break;

                case 'content-length':
                    if (!\is_int($value)) {
                        continue 2;
                    }

                    $newArray[Constants::FILE_SIZE] = $value;
                    break;

                case 'content-type':
                    $newArray[Constants::FILE_TYPE] = (string) $value;
                    break;

                case 'last-modified':
                    if (!\is_string($value)) {
                        continue 2;
                    }

                    $datetime = new \DateTimeImmutable($value);
                    $newArray[Constants::FILE_MODIFICATION_TIME] = (int) $datetime->format('U');

                    // no break
                case 'width':
                    if (!\is_int($value)) {
                        continue 2;
                    }

                    $newArray[Constants::MEDIA_WIDTH] = $value;
                    break;

                case 'height':
                    if (!\is_int($value)) {
                        continue 2;
                    }

                    $newArray[Constants::MEDIA_HEIGHT] = $value;
                    break;
            }
        }

        return $newArray;
    }
}
