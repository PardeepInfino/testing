<?php
namespace Aheadworks\RequisitionLists\ViewModel\Catalog\Product\Renderer\Swatches;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class ConfigurableProvider
 * @package Aheadworks\RequisitionLists\ViewModel\Catalog\Product\Renderer\Swatches
 */
class ConfigurableProvider implements ArgumentInterface
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param JsonSerializer $jsonSerializer
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        JsonSerializer $jsonSerializer,
        ProductMetadataInterface $productMetadata
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Get attribute prefix
     *
     * @return bool|string
     */
    public function getAttributePrefix()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $prefix = version_compare($magentoVersion, '2.4.0', '>=')
            ? 'data-'
            : '';

        return $this->jsonSerializer->serialize($prefix);
    }
}
