<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Ui\Component\Form\Button;

class DeleteButton extends AbstractButton
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];

        if ($this->isAllowed()) {
            $data =  [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to delete the shipping area?'
                    ) . '\', \'' . $this->getUrl('*/*/delete', ['id' => $this->getCurrentId()]) . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }
}
