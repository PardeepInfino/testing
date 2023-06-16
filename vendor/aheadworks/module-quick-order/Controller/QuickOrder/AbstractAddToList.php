<?php
namespace Aheadworks\QuickOrder\Controller\QuickOrder;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;

/**
 * Class AbstractAddToList
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder
 */
abstract class AbstractAddToList extends Action
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var OperationManager
     */
    protected $operationManager;

    /**
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param OperationManager $operationManager
     */
    public function __construct(
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        OperationManager $operationManager
    ) {
        parent::__construct($context);
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->operationManager = $operationManager;
    }

    /**
     * Convert to result array
     *
     * @param OperationResultInterface $operationResult
     * @return array
     */
    protected function convertToResultArray($operationResult)
    {
        return $this->dataObjectProcessor->buildOutputDataArray(
            $operationResult,
            OperationResultInterface::class
        );
    }
}
