<?php
namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\Column;

use Aheadworks\RequisitionLists\Model\Product\DetailProvider\Pool;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Pricing\Adjustment\Calculator as AdjustmentCalculator;

/**
 * Class Price
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\Column
 */
class Price extends Column
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var RendererPool
     */
    private $rendererPool;

    /**
     * @var AdjustmentCalculator
     */
    private $adjustmentCalculator;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var PriceCurrency
     */
    private $priceCurrency;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductRepositoryInterface $productRepository
     * @param LayoutFactory $layoutFactory
     * @param AdjustmentCalculator $adjustmentCalculator
     * @param Pool $pool
     * @param PriceCurrency $priceCurrency
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $productRepository,
        LayoutFactory $layoutFactory,
        AdjustmentCalculator $adjustmentCalculator,
        Pool $pool,
        PriceCurrency $priceCurrency,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->productRepository = $productRepository;
        $this->layoutFactory = $layoutFactory;
        $this->adjustmentCalculator = $adjustmentCalculator;
        $this->pool = $pool;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * {@inheritdoc}
     *
     * @throws LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['price'] = $this->getPriceHtml($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get render pool
     *
     * @return bool|BlockInterface
     * @throws LocalizedException
     */
    private function getRenderPool()
    {
        if ($this->rendererPool === null) {
            $layout = $this->layoutFactory->create(['cacheable' => false]);
            $layout->getUpdate()->load('catalog_product_prices');
            $layout->generateXml();
            $layout->generateElements();
            $this->rendererPool = $layout->getBlock('render.product.prices');
        }

        return $this->rendererPool;
    }

    /**
     * Get price html
     *
     * @param array $item
     * @return string
     * @throws LocalizedException
     */
    private function getPriceHtml($item)
    {
        $provider = $this->pool->getProvider($item);
        $rendererPool = $this->getRenderPool();
        $price = $provider->getFinalPriceForBuyRequest();
        if ($price) {
            $amount = $this->adjustmentCalculator->getAmount(
                $this->priceCurrency->convert($price),
                $provider->getProduct()
            );
            $priceRender = $rendererPool->createAmountRender($amount, $provider->getProduct());
        } else {
            $priceRender = $rendererPool->createPriceRender(
                FinalPrice::PRICE_CODE,
                $provider->getProduct(),
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );

        }

        return $priceRender->toHtml();
    }
}
