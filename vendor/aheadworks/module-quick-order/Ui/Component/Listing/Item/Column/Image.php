<?php
namespace Aheadworks\QuickOrder\Ui\Component\Listing\Item\Column;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Class Image
 *
 * @package Aheadworks\QuickOrder\Ui\Component\Listing\Item\Column
 */
class Image extends Column
{
    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductDetailPool $productDetailPool
     * @param ImageHelper $imageHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductDetailPool $productDetailPool,
        ImageHelper $imageHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->productDetailPool = $productDetailPool;
        $this->imageHelper = $imageHelper;
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
                    $provider = $this->productDetailPool->get($item);
                    $imageHelper = $this->imageHelper->init(
                        $provider->getProductForImage(),
                        $this->getData('config/image_id')
                    );
                    $item['product_image_url'] = $imageHelper->getUrl();
                    $item['product_name_url'] = $provider->getProductUrl();
                    $item['product_image_label'] = $imageHelper->getLabel();
                } catch (NoSuchEntityException $e) {
                }
            }
        }

        return $dataSource;
    }
}
