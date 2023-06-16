<?php
namespace Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item;

use Aheadworks\RequisitionLists\Model\RequisitionList\Item\ObjectDataProcessor;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item as RequisitionListItem;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item as RequisitionListItemResource;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @package Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = RequisitionListItemInterface::ITEM_ID;

    /**
     * @var ObjectDataProcessor
     */
    private $objectDataProcessor;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ObjectDataProcessor $objectDataProcessor
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ObjectDataProcessor $objectDataProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->objectDataProcessor = $objectDataProcessor;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(RequisitionListItem::class, RequisitionListItemResource::class);
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        /** @var RequisitionListItem $item */
        foreach ($this as $item) {
            $this->objectDataProcessor->prepareDataAfterLoad($item);
        }
        return parent::_afterLoad();
    }
}
