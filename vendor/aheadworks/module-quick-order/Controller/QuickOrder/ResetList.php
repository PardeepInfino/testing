<?php
namespace Aheadworks\QuickOrder\Controller\QuickOrder;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aheadworks\QuickOrder\Model\Url;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;

/**
 * Class ResetList
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder
 */
class ResetList extends Action
{
    /**
     * @var OperationManager
     */
    private $operationManager;

    /**
     * @param Context $context
     * @param OperationManager $operationManager
     */
    public function __construct(
        Context $context,
        OperationManager $operationManager
    ) {
        parent::__construct($context);
        $this->operationManager = $operationManager;
    }

    /**
     * Reset product list
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $result = $this->operationManager->resetCurrentProductList();
            $errors = $result->getErrorMessages();
            if (count($errors)) {
                foreach ($errors as $error) {
                    $this->messageManager->addErrorMessage($error->getText());
                }
            } else {
                $this->messageManager->addSuccessMessage(__('The list has been reset'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while resetting the list'));
        }

        return $resultRedirect->setPath(Url::QUICK_ORDER_ROUTE);
    }
}
