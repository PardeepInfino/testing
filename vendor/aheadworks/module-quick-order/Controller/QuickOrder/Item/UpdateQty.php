<?php
namespace Aheadworks\QuickOrder\Controller\QuickOrder\Item;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;

/**
 * Class UpdateQty
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder\Item
 */
class UpdateQty extends AbstractAction
{
    /**
     * Update item quantity
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $itemKey = $this->getRequest()->getParam(ProductListItemInterface::ITEM_KEY);
        if (!$itemKey) {
            $result = [
                'error' => __('Product list item key is required'),
            ];

            return $resultJson->setData($result);
        }
        $qty = $this->getRequest()->getParam(ProductListItemInterface::PRODUCT_QTY);
        if (!$qty) {
            $result = [
                'error' => __('Product quantity is required'),
            ];

            return $resultJson->setData($result);
        }

        try {
            $storeId = $this->storeManager->getStore()->getId();
            $operationResult = $this->operationManager->updateItemQty($itemKey, $qty, $storeId);
            $result = $this->convertToResultArray($operationResult);
        } catch (\Exception $exception) {
            $result = [
                'error' => $exception->getMessage(),
            ];
        }
        return $resultJson->setData($result);
    }
}
