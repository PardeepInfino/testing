<?php
namespace Aheadworks\QuickOrder\Controller\QuickOrder\Item;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;

/**
 * Class AbstractAction
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder\Item
 */
abstract class AbstractAction extends Action
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var OperationManager
     */
    protected $operationManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

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
