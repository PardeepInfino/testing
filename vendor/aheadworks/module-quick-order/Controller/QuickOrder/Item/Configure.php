<?php
namespace Aheadworks\QuickOrder\Controller\QuickOrder\Item;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\Product\View\Processor as ProductViewProcessor;

/**
 * Class Configure
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder\Item
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

        $itemKey = $this->getRequest()->getParam(ProductListItemInterface::ITEM_KEY);
        if (!$itemKey) {
            $result = [
                'error' => __('Product list item key is required'),
            ];

            return $resultJson->setData($result);
        }

        try {
            $storeId = $this->storeManager->getStore()->getId();
            $resultData = $this->productViewProcessor->getItemConfiguration($itemKey, $storeId);
        } catch (\Exception $exception) {
            $resultData = [
                'error' => $exception->getMessage(),
            ];
        }
        return $resultJson->setData($resultData);
    }
}
