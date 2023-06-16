<?php
namespace Aheadworks\QuickOrder\Controller\QuickOrder;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Model\FileSystem\Import\Csv as ImportCsv;
use Aheadworks\QuickOrder\Model\ProductList\RequestConverter;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;

/**
 * Class ImportFile
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder
 */
class ImportFile extends AddToListMultiple
{
    /**
     * File input name
     */
    const FILENAME = 'csv-file';

    /**
     * @var ImportCsv
     */
    protected $importCsv;

    /**
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param OperationManager $operationManager
     * @param RequestConverter $requestConverter
     * @param ImportCsv $importCsv
     */
    public function __construct(
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        OperationManager $operationManager,
        RequestConverter $requestConverter,
        ImportCsv $importCsv
    ) {
        parent::__construct($context, $dataObjectProcessor, $storeManager, $operationManager, $requestConverter);
        $this->importCsv = $importCsv;
    }

    /**
     * Import file
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $csvLines = $this->importCsv->getContent(self::FILENAME);
            $requestItems = $this->requestConverter->convertCsvLinesToRequestItems($csvLines);
            $storeId = $this->storeManager->getStore()->getId();
            $operationResult = $this->operationManager->addItemsToCurrentList($requestItems, $storeId);
            $result = $this->convertToResultArray($operationResult);

            $resultMessages = [];
            if ($operationResult->getSuccessMessages()) {
                $resultMessages[] = $this->getSuccessMessage($operationResult);
                $resultMessages = array_merge($resultMessages, $result['error_messages']);
            } else {
                $resultMessages = $this->getErrorMessage();
            }

            $result['messages'] = $resultMessages;
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }

        return $resultJson->setData($result);
    }
}
