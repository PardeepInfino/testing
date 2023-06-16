<?php
namespace Aheadworks\QuickOrder\Model\Toolbar\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\QuickOrder\Model\Toolbar\Layout\LayoutProcessorInterface;
use Aheadworks\QuickOrder\Model\Url;

/**
 * Class FormConfig
 *
 * @package Aheadworks\QuickOrder\Model\Toolbar\Layout\Processor
 */
class FormConfig implements LayoutProcessorInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     * @param Url $url
     */
    public function __construct(
        ArrayManager $arrayManager,
        Url $url
    ) {
        $this->arrayManager = $arrayManager;
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        $component = 'components/aw_quick_order_toolbar/children/individual_sku_tab/children/single-sku';
        $jsLayout = $this->arrayManager->merge(
            $component,
            $jsLayout,
            [
                'addToListUrl' => $this->url->getAddToListUrl()
            ]
        );

        $component = 'components/aw_quick_order_toolbar/children/multiple_sku_tab/children/multiple-sku';
        $jsLayout = $this->arrayManager->merge(
            $component,
            $jsLayout,
            [
                'addToListUrl' => $this->url->getMultipleAddToListUrl()
            ]
        );
        $component = 'components/aw_quick_order_toolbar/children/import_sku_tab/children/import-sku';
        $jsLayout = $this->arrayManager->merge(
            $component,
            $jsLayout,
            [
                'addToListUrl' => $this->url->getImportFileUrl(),
                'downloadSampleFileUrl' => $this->url->getUrlToDownloadSampleFileForImport(),
            ]
        );

        return $jsLayout;
    }
}
