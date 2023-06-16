<?php
namespace Aheadworks\QuickOrder\Controller\QuickOrder;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;
use Aheadworks\QuickOrder\Model\ProductList\RequestConverter;
use Aheadworks\QuickOrder\Api\Data\OperationResultMessageInterface as MessageInterface;
use Aheadworks\QuickOrder\Model\Source\QuickOrder\OperationResult\MessageType;

/**
 * Class AddToListMultiple
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder
 */
class AddToListMultiple extends AbstractAddToList
{
    /**
     * @var RequestConverter
     */
    protected $requestConverter;

    /**
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param OperationManager $operationManager
     * @param RequestConverter $requestConverter
     */
    public function __construct(
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        OperationManager $operationManager,
        RequestConverter $requestConverter
    ) {
        parent::__construct($context, $dataObjectProcessor, $storeManager, $operationManager);
        $this->requestConverter = $requestConverter;
    }

    /**
     * Add multiple sku to list
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $skuList = $this->getRequest()->getParam('sku_list');
        try {
            $requestItems = $this->requestConverter->convertSkuListToRequestItems($skuList);
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

    /**
     * Get success message
     *
     * @param OperationResultInterface $operationResult
     * @return array
     */
    protected function getSuccessMessage($operationResult)
    {
        return [
            MessageInterface::TYPE => MessageType::SUCCESS,
            MessageInterface::TITLE => '',
            MessageInterface::TEXT => __(
                '%1 item(s) are added to the list',
                count($operationResult->getSuccessMessages())
            )
        ];
    }

    /**
     * Get error message
     *
     * @return array
     */
    protected function getErrorMessage()
    {
        return [
            MessageInterface::TYPE => MessageType::ERROR,
            MessageInterface::TITLE => '',
            MessageInterface::TEXT => __('SKUs are not found. Please, check entered data')
        ];
    }
}
