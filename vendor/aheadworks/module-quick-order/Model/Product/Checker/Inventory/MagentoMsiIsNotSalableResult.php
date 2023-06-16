<?php
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySales\Model\IsProductSalableForRequestedQtyCondition\ProductSalabilityError;
use Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi\IsProductSalableForRequestedQtyFactory;
use Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi\StockResolverFactory;
use Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi\GetSkusByProductIdsFactory;

/**
 * Class MagentoMsiIsNotSalableResult
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory
 */
class MagentoMsiIsNotSalableResult implements IsNotSalableForRequestedQtyResultInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StockResolverFactory
     */
    private $stockResolverFactory;

    /**
     * @var IsProductSalableForRequestedQtyFactory
     */
    private $isProductSalableFactory;

    /**
     * @var GetSkusByProductIdsFactory
     */
    private $getSkusByProductIdsFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param StockResolverFactory $stockResolveFactory
     * @param IsProductSalableForRequestedQtyFactory $isProductSalableFactory
     * @param GetSkusByProductIdsFactory $getSkusByProductIdsFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StockResolverFactory $stockResolveFactory,
        IsProductSalableForRequestedQtyFactory $isProductSalableFactory,
        GetSkusByProductIdsFactory $getSkusByProductIdsFactory
    ) {
        $this->storeManager = $storeManager;
        $this->stockResolverFactory = $stockResolveFactory;
        $this->isProductSalableFactory = $isProductSalableFactory;
        $this->getSkusByProductIdsFactory = $getSkusByProductIdsFactory;
    }

    /**
     * @inheritdoc
     */
    public function getResultMessage($product, $requestedQty)
    {
        try {
            $message = '';
            $websiteCode = $this->storeManager->getWebsite($product->getStore()->getWebsiteId())->getCode();
            $stockResolver = $this->stockResolverFactory->create();
            $stock = $stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
            $isProductSalable = $this->isProductSalableFactory->create();
            $skus = $this->getSkusByProductIdsFactory->create()->execute([$product->getId()]);
            $isSalableResult = $isProductSalable->execute(
                $skus[$product->getId()],
                $stock->getStockId(),
                $requestedQty
            );
            if ($isSalableResult->isSalable() === false) {
                /** @var ProductSalabilityError $error */
                foreach ($isSalableResult->getErrors() as $error) {
                    $message = $error->getMessage();
                }
            }
        } catch (LocalizedException $exception) {
            $message = '';
        }

        return $message;
    }
}
