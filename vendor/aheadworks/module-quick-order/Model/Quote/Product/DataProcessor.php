<?php
namespace Aheadworks\QuickOrder\Model\Quote\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\Product\BuyRequest\Processor;

/**
 * Class DataProcessor
 *
 * @package Aheadworks\QuickOrder\Model\Quote\Product
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
     * @param ProductListItemInterface $item
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getProduct(ProductListItemInterface $item)
    {
        return $this->productRepository->get($item->getProductSku(), false, null, true);
    }

    /**
     * Get product buy request
     *
     * @param ProductListItemInterface $item
     * @return DataObject
     */
    public function getBuyRequest(ProductListItemInterface $item)
    {
        return $this->buyRequestProcessor->prepareBuyRequest($item);
    }
}
