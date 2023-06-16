<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterfaceFactory;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Save
 * @package Aheadworks\RequisitionLists\Controller\RList
 */
class Save extends AbstractRequisitionListAction
{
    /**
     * @var RequisitionListInterfaceFactory
     */
    private $requisitionListInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param Provider $provider
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListInterfaceFactory $requisitionListInterfaceFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Provider $provider,
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        RequisitionListInterfaceFactory $requisitionListInterfaceFactory,
        RequisitionListRepositoryInterface $requisitionListRepository,
        DataObjectHelper $dataObjectHelper,
        PageFactory $pageFactory
    ) {
        parent::__construct(
            $provider,
            $context,
            $customerSession,
            $responseFactory,
            $requisitionListRepository,
            $pageFactory
        );
        $this->dataObjectHelper = $dataObjectHelper;
        $this->requisitionListInterfaceFactory = $requisitionListInterfaceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->performSave();
            $this->messageManager->addSuccessMessage(__('Requisition List was successfully saved.'));
        } catch (CouldNotSaveException $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while save the Requisition List.')
            );
        }
        if ($listId = $this->getRequest()->getParam(RequisitionListInterface::LIST_ID, null)) {
            $resultRedirect->setPath('*/*/edit', [RequisitionListInterface::LIST_ID => $listId]);
        } else {
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        return $resultRedirect;
    }

    /**
     * @inheritdoc
     */
    protected function isEntityBelongsToCustomer()
    {
        return true;
    }

    /**
     * Save Requisition List
     */
    private function performSave()
    {
        $data = $this->getRequest()->getParams();
        $customerId = $this->customerSession->getCustomerId();
        /** @var RequisitionListInterface $listObject */
        $listObject = $this->requisitionListInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $listObject,
            $data,
            RequisitionListInterface::class
        );

        if (!$listObject->getCustomerId()) {
            $listObject->setCustomerId($customerId);
        }

        $this->requisitionListRepository->save($listObject);
    }
}
