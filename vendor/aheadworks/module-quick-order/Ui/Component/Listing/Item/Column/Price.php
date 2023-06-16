<?php
namespace Aheadworks\QuickOrder\Ui\Component\Listing\Item\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\View\LayoutFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;
use Magento\Framework\Pricing\Adjustment\Calculator as AdjustmentCalculator;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

/**
 * Class Price
 *
 * @package Aheadworks\QuickOrder\Ui\Component\Listing\Item\Column
 */
class Price extends Column
{
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var RendererPool
     */
    private $rendererPool;

    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @var AdjustmentCalculator
     */
    private $adjustmentCalculator;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductDetailPool $productDetailPool
     * @param LayoutFactory $layoutFactory
     * @param AdjustmentCalculator $adjustmentCalculator
     * @param PriceHelper $priceHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductDetailPool $productDetailPool,
        LayoutFactory $layoutFactory,
        AdjustmentCalculator $adjustmentCalculator,
        PriceHelper $priceHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->layoutFactory = $layoutFactory;
        $this->productDetailPool = $productDetailPool;
        $this->adjustmentCalculator = $adjustmentCalculator;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getName()] = $this->getPriceHtml($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get render pool
     *
     * @return bool|BlockInterface|RendererPool
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
        $provider = $this->productDetailPool->get($item);
        $rendererPool = $this->getRenderPool();
        $price = $provider->getFinalPriceForBuyRequest();
        if ($price) {
            $price = $this->priceHelper->currency($price, false, false);
            $amount = $this->adjustmentCalculator->getAmount(
                $price,
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
