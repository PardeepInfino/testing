<?php
namespace Aheadworks\QuickOrder\Model\FileSystem\Import;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\MediaStorage\Model\File\Uploader;

/**
 * Class Csv
 *
 * @package Aheadworks\QuickOrder\Model\FileSystem\Import
 */
class Csv
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var array
     */
    private $allowedExtensions;

    /**
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param array $allowedExtensions
     */
    public function __construct(
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        $allowedExtensions = []
    ) {
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Get newly loaded file content
     *
     * @param string $fileId
     * @return array
     * @throws FileSystemException
     */
    public function getContent($fileId)
    {
        /** @var Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->allowedExtensions);
        $file = $uploader->validateFile();

        $readDir = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $stream = $readDir->openFile($readDir->getRelativePath($file['tmp_name']));
        $lines = [];
        while ($line = $stream->readCsv()) {
            $lines[] = $line;
        }
        $stream->close();

        return $lines;
    }
}
