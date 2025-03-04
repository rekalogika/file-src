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

namespace Rekalogika\File\Bridge\Symfony\HttpFoundation;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\File\File;
use Rekalogika\File\RawMetadata;
use Symfony\Component\HttpFoundation\File\File as HttpFoundationFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Adapter to convert a HttpFoundation File to a FileInterface object.
 */
final class FromHttpFoundationFileAdapter extends File
{
    private ?RawMetadata $cachedMetadata = null;

    private function __construct(
        private readonly HttpFoundationFile $source,
    ) {
        parent::__construct($this->source->getRealPath());
    }

    public static function adapt(HttpFoundationFile $source): FileInterface
    {
        // prevent adaptception
        if ($source instanceof ToHttpFoundationFileAdapter) {
            return $source->getWrapped();
        }

        return new self($source);
    }

    public function getWrapped(): HttpFoundationFile
    {
        return $this->source;
    }

    #[\Override]
    protected function getRawMetadata(): RawMetadata
    {
        if ($this->cachedMetadata !== null) {
            return $this->cachedMetadata;
        }

        $metadata = parent::getRawMetadata();

        if ($this->source instanceof UploadedFile) {
            $metadata->set(Constants::FILE_NAME, $this->source->getClientOriginalName());
        }

        return $this->cachedMetadata = $metadata;
    }
}
