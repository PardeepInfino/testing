<?php
namespace Aheadworks\RequisitionLists\Model\ResourceModel;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as FrameworkAbstractCollection;

/**
 * Class AbstractCollection
 * @package Aheadworks\RequisitionLists\Model\ResourceModel
 */
class AbstractCollection extends FrameworkAbstractCollection
{
    /**
     * @var string[]
     */
    private $linkageTableNames = [];

    /**
     * Attach relation table data to collection items
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string|array $columnNameRelationTable
     * @param string $fieldName
     * @return void
     */
    public function attachRelationTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable,
        $fieldName
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from([$tableName . '_table' => $this->getTable($tableName)])
                ->where($tableName . '_table.' . $linkageColumnName . ' IN (?)', $ids);
            $result = $connection->fetchAll($select);

            /** @var DataObject $item */
            foreach ($this as $item) {
                $resultIds = [];
                $id = $item->getData($columnName);
                foreach ($result as $data) {
                    if ($data[$linkageColumnName] == $id) {
                        if (is_array($columnNameRelationTable)) {
                            $fieldValue = [];
                            foreach ($columnNameRelationTable as $columnNameRelation) {
                                $fieldValue[$columnNameRelation] = $data[$columnNameRelation];
                            }
                            $resultIds[] = $fieldValue;
                        } else {
                            $resultIds[] = $data[$columnNameRelationTable];
                        }
                    }
                }
                $item->setData($fieldName, $resultIds);
            }
        }
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnFilter
     * @param string $fieldName
     * @return $this
     */
    public function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnFilter,
        $fieldName
    ) {
        if (!$this->getFilter($columnFilter)) {
            $linkageTableName = $columnFilter . '_table';
            if (in_array($linkageTableName, $this->linkageTableNames)) {
                $this->addFilterToMap($columnFilter, $columnFilter . '_table.' . $fieldName);
                return $this;
            }

            $this->linkageTableNames[] = $linkageTableName;
            $select = $this->getSelect();
            $select->joinLeft(
                [$linkageTableName => $this->getTable($tableName)],
                'main_table.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );

            $this->addFilterToMap($columnFilter, $columnFilter . '_table.' . $fieldName);
        }

        return $this;
    }

    /**
     * Retrieve data from table
     *
     * @param string $tableName
     * @param string $linkageColumnName
     * @param array $ids
     * @return array
     */
    protected function getDataFromTable($tableName, $linkageColumnName, $ids)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from([$tableName . '_table' => $this->getTable($tableName)])
            ->where($tableName . '_table.' . $linkageColumnName . ' IN (?)', $ids);

        return $connection->fetchAll($select);
    }
}
