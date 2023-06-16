<?php
namespace Aheadworks\QuickOrder\Model\ProductList\Item;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterfaceFactory;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Model\Exception\OperationException;
use Aheadworks\QuickOrder\Api\ProductListItemRepositoryInterface;

/**
 * Class Processor
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item
 */
class Processor
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductListItemInterfaceFactory
     */
    private $productListItemFactory;

    /**
     * @var ProductListItemRepositoryInterface
     */
    private $productListItemRepository;

    /**
     * @var CompositeProcessor
     */
    private $compositeProcessor;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductListItemInterfaceFactory $productListItemFactory
     * @param ProductListItemRepositoryInterface $productListItemRepository
     * @param CompositeProcessor $compositeProcessor
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductListItemInterfaceFactory $productListItemFactory,
        ProductListItemRepositoryInterface $productListItemRepository,
        CompositeProcessor $compositeProcessor
    ) {
        $this->productRepository = $productRepository;
        $this->productListItemFactory = $productListItemFactory;
        $this->productListItemRepository = $productListItemRepository;
        $this->compositeProcessor = $compositeProcessor;
    }

    /**
     * Create product list item
     *
     * @param ItemDataInterface $requestItem
     * @param int $storeId
     * @return ProductListItemInterface
     * @throws OperationException
     */
    public function create($requestItem, $storeId)
    {
        /** @var ProductListItemInterface $item */
        $item = $this->productListItemFactory->create();
        try {
            $product = $this->productRepository->get($requestItem->getProductSku(), false, $storeId);
        } catch (NoSuchEntityException $exception) {
            throw new OperationException(__($exception->getMessage()));
        }
        $productListItem = $this->compositeProcessor->process($requestItem, $item, $product);

        return $productListItem;
    }

    /**
     * Update product list item
     *
     * @param string $itemKey
     * @param ItemDataInterface $requestItem
     * @param OperationResultInterface $operationResult
     * @param int $storeId
     * @return bool
     */
    public function update($itemKey, $requestItem, $operationResult, $storeId)
    {
        try {
            $item = $this->productListItemRepository->getByKey($itemKey);
        } catch (NoSuchEntityException $exception) {
            $operationResult->addErrorMessage(__('Item key: ', $itemKey), $exception->getMessage());
            return false;
        }

        $messageTitle = __('SKU: %1', $item->getProductSku());
        try {
            $product = $this->productRepository->getById($item->getProductId(), false, $storeId);
            $productListItem = $this->compositeProcessor->process($requestItem, $item, $product);
            $this->productListItemRepository->save($productListItem);
        } catch (\Exception $exception) {
            $operationResult->addErrorMessage($messageTitle, $exception->getMessage());
            return false;
        }

        $operationResult->addSuccessMessage($messageTitle, __('The product has been updated'));

        return true;
    }

    /**
     * Remove item
     *
     * @param string $itemKey
     * @param OperationResultInterface $operationResult
     * @return bool
     */
    public function remove($itemKey, $operationResult)
    {
        try {
            $item = $this->productListItemRepository->getByKey($itemKey);
        } catch (NoSuchEntityException $exception) {
            $operationResult->addErrorMessage(__('Item ID: ', $itemKey), $exception->getMessage());
            return false;
        }
        try {
            $this->productListItemRepository->deleteById($item->getItemId());
        } catch (CouldNotDeleteException $exception) {
            $operationResult->addErrorMessage(__('SKU: %1', $item->getProductSku()), $exception->getMessage());
            return false;
        }

        $operationResult->addSuccessMessage(__('SKU: %1', $item->getProductSku()), __('The product has been removed'));

        return true;
    }
}
