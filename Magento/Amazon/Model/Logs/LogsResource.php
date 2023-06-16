<?php

declare(strict_types=1);

namespace Magento\Amazon\Model\Logs;

use Magento\Amazon\Api\Data\AccountInterface;

class LogsResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_logs',
            'id'
        );
    }

    public function getLogs(
        AccountInterface $account,
        ?string $type = null,
        ?string $action = null,
        ?int $afterId = null,
        int $limit = 1000
    ): array {
        $select = $this->getConnection()->select();
        $select->from(
            ['log' => $this->getMainTable()],
            [
                'id',
                'external_id',
                'merchant_id',
                'identifier',
                'type',
                'action',
                'log'
            ]
        )->where('merchant_id = ?', (int)$account->getMerchantId());
        if ($type) {
            $select->where('type = ?', $type);
        }
        if ($action) {
            $select->where('action = ?', $action);
        }
        if ($afterId) {
            $select->where('id > ?', $afterId);
        }
        $select->limit($limit);
        $select->order('id ASC');
        return $this->getConnection()->fetchAssoc($select);
    }

    public function insertLogs(AccountInterface $account, array $logs): void
    {
        if (!$logs) {
            return;
        }

        $preparedLogs = [];
        $merchantId = (int)$account->getMerchantId();
        foreach ($logs as $log) {
            $preparedLogs[] = [
                'external_id' => $log['id'],
                'merchant_id' => $merchantId,
                'identifier' => $log['identifier'] ?? null,
                'type' => $log['type'] ?? '',
                'action' => $log['action'] ?? '',
                'log' => $log['log'] ?? '',
            ];
        }

        $this->getConnection()->insertOnDuplicate(
            $this->getMainTable(),
            $preparedLogs,
            ['log']
        );
    }

    public function deleteByIds(AccountInterface $account, array $logsIds)
    {
        if (!$logsIds) {
            return;
        }
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['merchant_id = ?' => (int)$account->getMerchantId(), 'id in (?)' => $logsIds]
        );
    }
}
