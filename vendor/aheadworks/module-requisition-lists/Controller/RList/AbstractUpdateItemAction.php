<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\Collection;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\CollectionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class AbstractUpdateItemAction
 *
 * @package Aheadworks\RequisitionLists\Controller\RList
 */
abstract class AbstractUpdateItemAction extends AbstractRequisitionListAction
{
    /**
     * @var RequisitionListItemRepositoryInterface
     */
    protected $requisitionListItemRepository;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

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
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($provider, $context, $customerSession,
            $responseFactory, $requisitionListRepository, $pageFactory);
        $this->requisitionListItemRepository = $requisitionListItemRepository;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $listId = $this->getRequest()->getParam(RequisitionListInterface::LIST_ID, null);
        $resultRedirect->setPath('*/*/edit', [RequisitionListInterface::LIST_ID => $listId]);

        return $this->update($resultRedirect);
    }

    /**
     * Update requisition list/item instance
     *
     * @param $resultRedirect
     */
    abstract protected function update($resultRedirect);

    /**
     * Get selected items
     *
     * @return DataObject[]
     * @throws LocalizedException
     */
    protected function getItems()
    {
        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        return $collection->getItems();
    }
}
