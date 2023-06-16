<?php
namespace Aheadworks\QuickOrder\Model\ProductList;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterfaceFactory;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterfaceFactory;
use Aheadworks\QuickOrder\Api\ProductListManagementInterface;
use Aheadworks\QuickOrder\Api\ProductListItemRepositoryInterface;
use Aheadworks\QuickOrder\Model\Product\Option\Converter as OptionConverter;

/**
 * Class OperationManager
 *
 * @package Aheadworks\QuickOrder\Model\ProductList
 */
class OperationManager
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SessionManager
     */
    private $listSessionManager;

    /**
     * @var ItemDataInterfaceFactory
     */
    private $itemDataFactory;

    /**
     * @var ProductListManagementInterface
     */
    private $productListManagement;

    /**
     * @var ProductListItemRepositoryInterface
     */
    private $productListItemRepository;

    /**
     * @var OptionConverter
     */
    private $optionConverter;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param ItemDataInterfaceFactory $itemDataFactory
     * @param ProductListManagementInterface $productListManagement
     * @param ProductListItemRepositoryInterface $productListItemRepository
     * @param SessionManager $listSessionManager
     * @param OptionConverter $optionConverter
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        ItemDataInterfaceFactory $itemDataFactory,
        ProductListManagementInterface $productListManagement,
        ProductListItemRepositoryInterface $productListItemRepository,
        SessionManager $listSessionManager,
        OptionConverter $optionConverter
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->itemDataFactory = $itemDataFactory;
        $this->productListManagement = $productListManagement;
        $this->productListItemRepository = $productListItemRepository;
        $this->listSessionManager = $listSessionManager;
        $this->optionConverter = $optionConverter;
    }

    /**
     * Add items to current list
     *
     * @param array $itemDataArray
     * @param int $storeId
     * @return OperationResultInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function addItemsToCurrentList($itemDataArray, $storeId)
    {
        $requestItems = [];
        foreach ($itemDataArray as $itemData) {
            /** @var ItemDataInterface $itemData */
            $requestItem = $this->itemDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $requestItem,
                $itemData,
                ItemDataInterface::class
            );
            $requestItems[] = $requestItem;
        }

        $listId = $this->listSessionManager->getActiveListIdForCurrentUser(true);
        $result = $this->productListManagement->addItemsToList($listId, $requestItems, $storeId);
        $this->listSessionManager->saveListForCurrentUser();

        return $result;
    }

    /**
     * Update item option
     *
     * @param string $itemKey
     * @param array $buyRequest
     * @param int $storeId
     * @return OperationResultInterface
     * @throws NoSuchEntityException
     */
    public function updateItemOption($itemKey, $buyRequest, $storeId)
    {
        /** @var ItemDataInterface $requestItem */
        $requestItem = $this->itemDataFactory->create();
        $item = $this->productListItemRepository->getByKey($itemKey);
        $productOption = $this->optionConverter->toProductOptionObject($item->getProductType(), $buyRequest);
        $requestItem->setProductOption($productOption);
        return $this->productListManagement->updateItem($itemKey, $requestItem, $storeId);
    }

    /**
     * Update item quantity
     *
     * @param string $itemKey
     * @param float|int $qty
     * @param int $storeId
     * @return OperationResultInterface
     */
    public function updateItemQty($itemKey, $qty, $storeId)
    {
        /** @var ItemDataInterface $requestItem */
        $requestItem = $this->itemDataFactory->create();
        $requestItem->setProductQty($qty);
        return $this->productListManagement->updateItem($itemKey, $requestItem, $storeId);
    }

    /**
     * Reset current product list
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function resetCurrentProductList()
    {
        $list = $this->listSessionManager->getActiveListForCurrentUser();
        return $this->productListManagement->removeAllItemsFromList($list->getListId());
    }
}
