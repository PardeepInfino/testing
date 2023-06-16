<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem as Filesystem;
use Magento\Framework\Filesystem\Io\File;

class IndexFileParts
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $ioFile;

    public function __construct(
        Filesystem $filesystem,
        File $ioFile
    ) {
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
    }

    public function getIndexFileParts(Sitemap $sitemap): array
    {
        $num = 1;
        $result = [];

        while (true) {
            $numeratedFilename = $this->getNumeratedFileName($sitemap['path'], $num++);

            if ($numeratedFilename && $this->isFileExists($numeratedFilename)) {
                $result[] = $numeratedFilename;
            } else {
                break;
            }
        }

        return $result;
    }

    private function getNumeratedFileName(string $fileName, int $num): string
    {
        return str_replace('.xml', sprintf('_%d.xml', $num), $fileName);
    }

    private function isFileExists(string $filePath): bool
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath() . $filePath;

        return $this->ioFile->fileExists($path);
    }
}
