<?php
namespace Aheadworks\RequisitionLists\Model\Quote\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\Product\BuyRequest\Processor;

/**
 * Class DataProcessor
 * @package Aheadworks\RequisitionLists\Model\Quote\Product
 */
class DataProcessor
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Processor
     */
    private $buyRequestProcessor;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Processor $buyRequestProcessor
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Processor $buyRequestProcessor
    ) {
        $this->productRepository = $productRepository;
        $this->buyRequestProcessor = $buyRequestProcessor;
    }

    /**
     * Get product
     *
     * @param RequisitionListItemInterface $item
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getProduct(RequisitionListItemInterface $item)
    {
        return $this->productRepository->getById($item->getProductId(), false, null, true);
    }

    /**
     * Get product buy request
     *
     * @param RequisitionListItemInterface $item
     * @return DataObject
     */
    public function getBuyRequest(RequisitionListItemInterface $item)
    {
        return $this->buyRequestProcessor->prepareBuyRequest($item);
    }
}
