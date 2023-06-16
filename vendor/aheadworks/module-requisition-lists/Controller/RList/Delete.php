<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 * @package Aheadworks\RequisitionLists\Controller\RList
 */
class Delete extends AbstractRequisitionListAction
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');
        $listId = $this->getRequest()->getParam(RequisitionListInterface::LIST_ID, null);
        if ($listId) {
            try {
                $this->requisitionListRepository->deleteById($listId);
                $this->messageManager->addSuccessMessage(
                    __('Requisition List has been deleted.')
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t delete Requisition List right now.')
                );
            }
        } else {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while delete Requisition List.')
            );
        }

        return $resultRedirect;
    }
}
