<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Ui\Component\Form\Label\Buttons;

use Amasty\Label\ViewModel\Adminhtml\Labels\Edit\GetCurrentLabelData;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete implements ButtonProviderInterface
{
    public const ID = 'id';

    /**
     * @var GetCurrentLabelData
     */
    private $getCurrentLabelData;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        GetCurrentLabelData $getCurrentLabelData,
        UrlInterface $urlBuilder
    ) {
        $this->getCurrentLabelData = $getCurrentLabelData;
        $this->urlBuilder = $urlBuilder;
    }

    public function getButtonData()
    {
        $buttonData = [];

        if (!$this->getCurrentLabelData->isNewLabel()) {
            $buttonData = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 222
            ];
        }

        return $buttonData;
    }

    private function getDeleteUrl(): string
    {
        return $this->urlBuilder->getUrl(
            '*/*/delete',
            [self::ID => $this->getCurrentLabelData->getLabelId()]
        );
    }
}
