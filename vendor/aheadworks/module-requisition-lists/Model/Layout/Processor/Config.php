<?php
namespace Aheadworks\RequisitionLists\Model\Layout\Processor;

use Aheadworks\RequisitionLists\Model\Layout\LayoutProcessorInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Aheadworks\RequisitionLists\Model\Url;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class Config
 * @package Aheadworks\RequisitionLists\Model\Toolbar\Layout\Processor
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
     * @var Provider
     */
    private $provider;

    /**
     * @param ArrayManager $arrayManager
     * @param Provider $provider
     * @param Url $url
     */
    public function __construct(
        ArrayManager $arrayManager,
        Provider $provider,
        Url $url
    ) {
        $this->arrayManager = $arrayManager;
        $this->url = $url;
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        $component = 'components/aw_requisition_list_config';
        $jsLayout = $this->arrayManager->merge(
            $component,
            $jsLayout,
            [
                'configureItemUrl' => $this->url->getConfigureItemUrl(),
                'updateItemOptionUrl' => $this->url->getUpdateItemOptionUrl(),
                'removeItemUrl' => $this->url->getRemoveItemUrl(
                    $this->provider->getRequisitionListId()
                ),
            ]
        );

        return $jsLayout;
    }
}
