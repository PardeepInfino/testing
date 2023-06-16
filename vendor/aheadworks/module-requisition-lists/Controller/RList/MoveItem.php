<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListManagementInterface;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\CollectionFactory;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterfaceFactory;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;

/**
 * Class MoveItem
 *
 * @package Aheadworks\RequisitionLists\Controller\RList
 */
class MoveItem extends AbstractUpdateItemAction
{
    /**
     * @var RequisitionListManagementInterface
     */
    private $requisitionListService;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param PageFactory $pageFactory
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
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
        $this->requisitionListService = $requisitionListService;
    }

    /**
     * @inheritDoc
     */
    protected function update($resultRedirect)
    {
        $items = $this->getItems();
        $moveToListId = $this->getRequest()->getParam('move_to_list', null);

        if (!$moveToListId) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while move items to Requisition List.')
            );

            return $resultRedirect;
        }

        if ($items) {
            try {
                $moveToList = $this->requisitionListRepository->get($moveToListId);

                /** @var RequisitionListItemInterface $item */
                foreach ($items as $item) {
                    $this->requisitionListService->moveItem($item, $moveToListId);
                }

                $this->messageManager->addComplexSuccessMessage(
                    'moveItemsToListSuccessMessage',
                    [
                        'count' => count($items),
                        'requisition_list_name' => $moveToList->getName(),
                        'requisition_list_url' => $this->_url->getUrl(
                            'aw_rl/rlist/edit/',
                            [
                                '_secure' => true,
                                RequisitionListItemInterface::LIST_ID => $moveToListId
                            ]
                        )
                    ]
                );
            } catch (NoSuchEntityException | CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while moved to Requisition List.')
                );
            }
        } else {
            $this->messageManager->addErrorMessage(
                __(
                    'Something went wrong while moved to Requisition List.'
                )
            );
        }

        return $resultRedirect;
    }
}
