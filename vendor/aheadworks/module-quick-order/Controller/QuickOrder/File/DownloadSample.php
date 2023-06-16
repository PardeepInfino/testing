<?php
namespace Aheadworks\QuickOrder\Controller\QuickOrder\File;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Aheadworks\QuickOrder\Model\FileSystem\Export\Csv as ExportCsv;

/**
 * Class DownloadSample
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder\File
 */
class DownloadSample extends Action
{
    /**
     * Sample file name
     */
    const SAMPLE_FILE_NAME = 'Products.csv';

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ExportCsv
     */
    private $exportCsv;

    /**
     * @var array
     */
    private $sampleProductData;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param ExportCsv $exportCsv
     * @param array $sampleProductData
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        ExportCsv $exportCsv,
        $sampleProductData = []
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->exportCsv = $exportCsv;
        $this->sampleProductData = $sampleProductData;
    }

    /**
     * Download sample file for import
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        $fileContent = $this->exportCsv->createFile($this->sampleProductData);
        return $this->fileFactory->create(self::SAMPLE_FILE_NAME, $fileContent, DirectoryList::VAR_DIR);
    }
}
