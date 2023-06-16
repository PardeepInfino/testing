<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2016-03-13T19:37:15+00:00
 * File:          Block/Adminhtml/Source/Edit.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Source;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize edit block
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Xtento_TrackingImport';
        $this->_controller = 'adminhtml_source';
        parent::_construct();

        if ($this->registry->registry('trackingimport_source')->getId()) {
            $this->buttonList->update('save', 'label', __('Save Source'));
            $this->buttonList->update('delete', 'label', __('Delete Source'));
            $this->buttonList->remove('reset');
        } else {
            $this->buttonList->remove('delete');
            $this->buttonList->remove('save');
        }

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save primary',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ]
        );
    }
}