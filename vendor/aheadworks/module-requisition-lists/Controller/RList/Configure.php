<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\Product\View\Processor as ProductViewProcessor;

/**
 * Class Configure
 *
 * @package Aheadworks\RequisitionLists\Controller\RequisitionLists\Item
 */
class Configure extends Action
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductViewProcessor
     */
    private $productViewProcessor;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ProductViewProcessor $productViewProcessor
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ProductViewProcessor $productViewProcessor
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->productViewProcessor = $productViewProcessor;
    }

    /**
     * Configure item
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $itemId = $this->getRequest()->getParam(RequisitionListItemInterface::ITEM_ID);
        if (!$itemId) {
            $result = [
                'error' => __('Requisition list item ID is required'),
            ];

            return $resultJson->setData($result);
        }

        try {
            $storeId = $this->storeManager->getStore()->getId();
            $resultData = $this->productViewProcessor->getItemConfiguration($itemId, $storeId);
        } catch (\Exception $exception) {
            $resultData = [
                'error' => $exception->getMessage(),
            ];
        }
        return $resultJson->setData($resultData);
    }
}
