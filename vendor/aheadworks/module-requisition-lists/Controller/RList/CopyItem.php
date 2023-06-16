<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\CollectionFactory;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterfaceFactory;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListManagementInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class CopyItem
 * @package Aheadworks\RequisitionLists\Controller\RList
 */
class CopyItem extends AbstractUpdateItemAction
{
    /**
     * @var RequisitionListItemInterfaceFactory
     */
    private $requisitionListItemInterfaceFactory;

    /**
     * @var RequisitionListManagementInterface
     */
    private $requisitionListService;

    /**
     * @param Provider $provider
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param PageFactory $pageFactory
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param RequisitionListItemInterfaceFactory $requisitionListItemInterfaceFactory
     * @param RequisitionListManagementInterface $requisitionListService
     */
    public function __construct(
        Provider $provider,
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        RequisitionListRepositoryInterface $requisitionListRepository,
        PageFactory $pageFactory,
        RequisitionListItemRepositoryInterface $requisitionListItemRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        RequisitionListItemInterfaceFactory $requisitionListItemInterfaceFactory,
        RequisitionListManagementInterface $requisitionListService
    ) {
        parent::__construct(
            $provider,
            $context,
            $customerSession,
            $responseFactory,
            $requisitionListRepository,
            $pageFactory,
            $requisitionListItemRepository,
            $filter,
            $collectionFactory
        );
        $this->requisitionListItemInterfaceFactory = $requisitionListItemInterfaceFactory;
        $this->requisitionListService = $requisitionListService;
    }

    /**
     * @inheritDoc
     */
    protected function update($resultRedirect)
    {
        $items = $this->getItems();
        $copyToListId = $this->getRequest()->getParam('copy_to_list', null);

        if (!$copyToListId) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while copy items to Requisition List.')
            );

            return $resultRedirect;
        }

        if ($items) {
            try {
                $copyToList = $this->requisitionListRepository->get($copyToListId);
                /** @var RequisitionListItemInterface $originalItem */
                foreach ($items as $originalItem) {
                    /** @var RequisitionListItemInterface $item */
                    $item = $this->requisitionListItemInterfaceFactory->create();
                    $this->requisitionListService->addItem(
                        $this->copyItemData($item, $originalItem, $copyToListId)
                    );
                }

                $this->messageManager->addComplexSuccessMessage(
                    'addItemsToListSuccessMessage',
                    [
                        'count' => count($items),
                        'requisition_list_name' => $copyToList->getName(),
                        'requisition_list_url' => $this->_url->getUrl(
                            'aw_rl/rlist/edit/',
                            [
                                '_secure' => true,
                                RequisitionListItemInterface::LIST_ID => $copyToListId
                            ]
                        )
                    ]
                );
            } catch (NoSuchEntityException | CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while copy to Requisition List.')
                );

                return $resultRedirect;
            }
        } else {
            $this->messageManager->addErrorMessage(
                __(
                    'Something went wrong while added to Requisition List.'
                )
            );
        }

        return $resultRedirect;
    }

    /**
     * Copy item data with new list id
     *
     * @param RequisitionListItemInterface $item
     * @param RequisitionListItemInterface$originalItem
     * @param int $newListId
     * @return RequisitionListItemInterface
     */
    private function copyItemData($item, $originalItem, $newListId)
    {
        $item->setData($originalItem->getData());
        $item->setItemId(null);
        $item->setListId($newListId);

        return $item;
    }
}
