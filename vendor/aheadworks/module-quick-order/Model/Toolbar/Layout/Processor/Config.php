<?php
namespace Aheadworks\QuickOrder\Model\Toolbar\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Model\Toolbar\Layout\LayoutProcessorInterface;
use Aheadworks\QuickOrder\Model\Url;
use Aheadworks\QuickOrder\Model\Config as ModuleConfig;

/**
 * Class Config
 *
 * @package Aheadworks\QuickOrder\Model\Toolbar\Layout\Processor
 */
class Config implements LayoutProcessorInterface
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
     * @var ModuleConfig
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ArrayManager $arrayManager
     * @param Url $url
     * @param ModuleConfig $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ArrayManager $arrayManager,
        Url $url,
        ModuleConfig $config,
        StoreManagerInterface $storeManager
    ) {
        $this->arrayManager = $arrayManager;
        $this->url = $url;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function process($jsLayout)
    {
        $component = 'components/aw_quick_order_config';
        $websiteId = $this->storeManager->getWebsite()->getId();

        $jsLayout = $this->arrayManager->merge(
            $component,
            $jsLayout,
            [
                'addToListUrl' => $this->url->getAddToListUrl(),
                'multipleAddToListUrl' => $this->url->getMultipleAddToListUrl(),
                'configureItemUrl' => $this->url->getConfigureItemUrl(),
                'updateItemOptionUrl' => $this->url->getUpdateItemOptionUrl(),
                'updateItemQtyUrl' => $this->url->getUpdateItemQtyUrl(),
                'removeItemUrl' => $this->url->getRemoveItemUrl(),
                'isAddToListButtonDisplayed' => $this->config->isAddToListButtonDisplayed($websiteId),
                'isQtyInputDisplayed' => $this->config->isQtyInputDisplayed($websiteId)
            ]
        );

        return $jsLayout;
    }
}
