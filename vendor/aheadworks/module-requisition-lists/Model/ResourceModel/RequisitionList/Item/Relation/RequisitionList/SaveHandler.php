<?php
namespace Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\Relation\RequisitionList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class SaveHandler
 * @package Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\Relation\RequisitionList
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->tableName = $this->resourceConnection->getTableName(RequisitionList::MAIN_TABLE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function execute($entity, $arguments = [])
    {
        $listId = $entity->getListId();
        if (!$listId) {
            return $entity;
        }

        try {
            $this->updateByEntity($listId);
        } catch (\Exception $e) {
            return $entity;
        }

        return $entity;
    }

    /**
     * Update update_at column by entity
     *
     * @param int $listId
     * @return int
     * @throws \Exception
     */
    private function updateByEntity($listId)
    {
        $whereCondition = new \Zend_Db_Expr(RequisitionListInterface::LIST_ID . ' = ' . $listId);
        $currentDate = (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT);

        return $this->resourceConnection->getConnection()
            ->update(
                $this->tableName,
                [
                    RequisitionListInterface::UPDATED_AT => $currentDate
                ],
                $whereCondition
            );
    }
}
