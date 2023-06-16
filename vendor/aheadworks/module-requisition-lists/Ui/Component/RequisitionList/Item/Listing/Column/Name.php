<?php
namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\Column;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\Product\DetailProvider\Pool;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface
    as ProductItemResolverInterface;

/**
 * Class Name
 *
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Item\Listing\Column
 */
class Name extends Column
{
    /**
     * @var ProductItemResolverInterface
     */
    private $productItemResolver;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductItemResolverInterface $productItemResolver
     * @param ProductRepositoryInterface $productRepository
     * @param Pool $pool
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductItemResolverInterface $productItemResolver,
        ProductRepositoryInterface $productRepository,
        Pool $pool,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->productItemResolver = $productItemResolver;
        $this->productRepository = $productRepository;
        $this->pool = $pool;
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
                try {
                    $provider = $this->pool->getProvider($item);

                    $item['image_html'] = $provider->getProductImageHtml();
                    $item['product_name_url'] = $provider->getProductUrl($item['product_qty']);
                    $item['is_available'] = $provider->isAvailableForSite();
                    $item['is_salable'] = $provider->isSalable();
                    $item['is_disabled'] = $provider->isDisabled();
                    $item['is_editable'] = $provider->isEditable();
                    $item['is_qty_enabled'] = $provider->isQtyEnabled();
                    $item['is_out_of_stock'] = !$provider->getQtyIsSalable($item['product_qty']);
                    $item['product_attributes'] =
                        $provider->getProductAttributes($item[RequisitionListItemInterface::PRODUCT_OPTION]);
                } catch (NoSuchEntityException $e) {
                    $item['is_available'] = false;
                    $item['product_attributes'] = [];
                }
            }
        }

        return $dataSource;
    }
}
