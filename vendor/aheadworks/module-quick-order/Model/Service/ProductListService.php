<?php
namespace Aheadworks\QuickOrder\Model\Service;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterfaceFactory;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterfaceFactory;
use Aheadworks\QuickOrder\Api\ProductListManagementInterface;
use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;
use Aheadworks\QuickOrder\Model\Exception\OperationException;
use Aheadworks\QuickOrder\Model\ProductList\Item\Processor as ItemProcessor;

/**
 * Class ProductListService
 *
 * @package Aheadworks\QuickOrder\Model\Service
 */
class ProductListService implements ProductListManagementInterface
{
    /**
     * @var ProductListRepositoryInterface
     */
    private $listRepository;

    /**
     * @var OperationResultInterfaceFactory
     */
    private $operationResultFactory;

    /**
     * @var ItemDataInterfaceFactory
     */
    private $itemDataFactory;

    /**
     * @var ItemProcessor
     */
    private $itemProcessor;

    /**
     * @param ProductListRepositoryInterface $listRepository
     * @param OperationResultInterfaceFactory $operationResultFactory
     * @param ItemDataInterfaceFactory $itemDataFactory
     * @param ItemProcessor $itemProcessor
     */
    public function __construct(
        ProductListRepositoryInterface $listRepository,
        OperationResultInterfaceFactory $operationResultFactory,
        ItemDataInterfaceFactory $itemDataFactory,
        ItemProcessor $itemProcessor
    ) {
        $this->listRepository = $listRepository;
        $this->operationResultFactory = $operationResultFactory;
        $this->itemDataFactory = $itemDataFactory;
        $this->itemProcessor = $itemProcessor;
    }

    /**
     * @inheritdoc
     */
    public function addItemsToList($listId, $itemsData, $storeId)
    {
        /** @var OperationResultInterface $operationResult */
        $operationResult = $this->operationResultFactory->create();
        $productList = $this->listRepository->get($listId);
        $productListItems = $productList->getItems() ?? [];
        foreach ($itemsData as $item) {
            if (!$item->getProductSku()) {
                continue;
            }
            $messageTitle = __('SKU: %1', $item->getProductSku());
            try {
                $productListItem = $this->itemProcessor->create($item, $storeId);
                $operationResult->addSuccessMessage($messageTitle, __('The product has been added to the list'));
                $operationResult->setLastAddedItemKey($productListItem->getItemKey());
                $productListItems[] = $productListItem;
            } catch (OperationException $exception) {
                $operationResult->addErrorMessage($messageTitle, $exception->getMessage());
            }
        }
        $productList->setItems($productListItems);
        $this->listRepository->save($productList);

        return $operationResult;
    }

    /**
     * @inheritdoc
     */
    public function removeAllItemsFromList($listId)
    {
        /** @var OperationResultInterface $operationResult */
        $operationResult = $this->operationResultFactory->create();
        $productList = $this->listRepository->get($listId);
        $productList->setItems([]);
        try {
            $this->listRepository->save($productList);
            $operationResult->addSuccessMessage('', __('All items have been removed from the list'));
        } catch (CouldNotSaveException $exception) {
            $operationResult->addErrorMessage('', $exception->getMessage());
        }

        return $operationResult;
    }

    /**
     * @inheritdoc
     */
    public function updateItem($itemKey, $requestItem, $storeId)
    {
        /** @var OperationResultInterface $operationResult */
        $operationResult = $this->operationResultFactory->create();
        $this->itemProcessor->update($itemKey, $requestItem, $operationResult, $storeId);

        return $operationResult;
    }

    /**
     * @inheritdoc
     */
    public function removeItem($itemKey)
    {
        /** @var OperationResultInterface $operationResult */
        $operationResult = $this->operationResultFactory->create();
        $this->itemProcessor->remove($itemKey, $operationResult);

        return $operationResult;
    }
}
