<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Model/ResourceModel/Log/Collection.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Model\ResourceModel\Log;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    protected function _construct()
    {
        $this->_init('Xtento\StockImport\Model\Log', 'Xtento\StockImport\Model\ResourceModel\Log');
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        if ($this->request->getControllerName() == 'log' ||
            ($this->request->getControllerName() == 'profile' && $this->request->getActionName() == 'log')
        ) {
            $this->getSelect()->joinLeft(
                ['profile' => $this->getTable('xtento_stockimport_profile')],
                'main_table.profile_id = profile.profile_id',
                ['concat(profile.name," (ID: ", profile.profile_id,")") as profile', 'profile.entity', 'profile.name']
            );
            if ($this->request->getParam('id', false)) {
                $this->addFieldToFilter('profile.profile_id', intval($this->request->getParam('id')));
            }
            if ($this->request->getParam('log_id', false) && !$this->request->getParam('ajax', false) == true) {
                $this->addFieldToFilter('log_id', intval($this->request->getParam('log_id')));
            }
        }

        return $this;
    }
}