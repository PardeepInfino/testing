<?php
namespace Aheadworks\RequisitionLists\Model\Service;

use Aheadworks\RequisitionLists\Api\RequisitionListManagementInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\Collection;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\CollectionFactory;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Comparator;

/**
 * Class RequisitionListService
 *
 * @package Aheadworks\RequisitionLists\Model\Service
 */
class RequisitionListService implements RequisitionListManagementInterface
{
    /**
     * @var CollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var RequisitionListItemRepositoryInterface
     */
    private $requisitionListItemRepository;

    /**
     * @var Comparator
     */
    private $itemComparator;

    /**
     * @param CollectionFactory $itemCollectionFactory
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param Comparator $comparator
     */
    public function __construct(
        CollectionFactory $itemCollectionFactory,
        RequisitionListItemRepositoryInterface $requisitionListItemRepository,
        Comparator $comparator
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->requisitionListItemRepository = $requisitionListItemRepository;
        $this->itemComparator = $comparator;
    }

    /**
     * @inheritdoc
     */
    public function addItem($item)
    {
        $foundItem = $this->getItemRepresentation($item);
        if ($foundItem) {
            $foundItem->setProductQty($foundItem->getProductQty() + $item->getProductQty());
            $item = $foundItem;
        }

        return $this->requisitionListItemRepository->save($item);
    }

    /**
     * @inheritdoc
     */
    public function moveItem($item, $listIdToMove)
    {
        $this->requisitionListItemRepository->delete($item);
        $item->setItemId(null);
        $item->setListId($listIdToMove);
        return $this->addItem($item);
    }

    /**
     * Get item representation
     *
     * @param RequisitionListItemInterface $item
     * @return RequisitionListItemInterface|null
     */
    private function getItemRepresentation($item)
    {
        /** @var Collection $itemCollection */
        $itemCollection = $this->itemCollectionFactory->create();
        $itemCollection->addFieldToFilter(
            RequisitionListItemInterface::LIST_ID,
            ['eq' => $item->getListId()]
        );
        /** @var RequisitionListItemInterface[] $listItems */
        $listItems = $itemCollection->getItems();
        foreach ($listItems as $listItem) {
            if ($this->itemComparator->compareIfEqual($listItem, $item)) {
                return $listItem;
            }
        }

        return null;
    }
}
