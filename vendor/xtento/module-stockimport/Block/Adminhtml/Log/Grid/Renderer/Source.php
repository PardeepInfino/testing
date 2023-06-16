<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Block/Adminhtml/Log/Grid/Renderer/Source.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Block\Adminhtml\Log\Grid\Renderer;

class Source extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public static $sources = [];

    /**
     * @var \Xtento\StockImport\Model\SourceFactory
     */
    protected $sourceFactory;

    /**
     * @var \Xtento\StockImport\Model\System\Config\Source\Source\Type
     */
    protected $sourceType;

    /**
     * Source constructor.
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Xtento\StockImport\Model\SourceFactory $sourceFactory
     * @param \Xtento\StockImport\Model\System\Config\Source\Source\Type $sourceType
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Xtento\StockImport\Model\SourceFactory $sourceFactory,
        \Xtento\StockImport\Model\System\Config\Source\Source\Type $sourceType,
        array $data = []
    ) {
        $this->sourceFactory = $sourceFactory;
        $this->sourceType = $sourceType;
        parent::__construct($context, $data);
    }

    /**
     * Render log
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $sourceIds = $row->getSourceIds();
        $sourceText = "";
        if (empty($sourceIds)) {
            return __('No source selected. Enable in the "Import Sources" tab of the profile.');
        }
        foreach (explode("&", $sourceIds) as $sourceId) {
            if (!empty($sourceId) && is_numeric($sourceId)) {
                if (!isset(self::$sources[$sourceId])) {
                    $source = $this->sourceFactory->create()->load(
                        $sourceId
                    );
                    self::$sources[$sourceId] = $source;
                } else {
                    $source = self::$sources[$sourceId];
                }
                if ($source->getId()) {
                    $sourceText .= $source->getName() . " (" . $this->sourceType->getName(
                            $source->getType()
                        ) . ")<br>";
                }
            }
        }
        return $sourceText;
    }
}
