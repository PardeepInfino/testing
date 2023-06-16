<?php

namespace Magento\Amazon\Model\SyncStatus;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;

class SyncStatusResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_UNKNOWN_ERROR = 'unknown-error';
    public const STATUS_API_ERROR = 'api-error';

    private const NEXT_TOKEN_FLAG_NAME = 'amazon_sales_channel_next_token';

    private static $statuses = [
        self::STATUS_SUCCESS,
        self::STATUS_UNKNOWN_ERROR,
        self::STATUS_API_ERROR,
    ];
    /**
     * @var \Magento\Framework\FlagManager
     */
    private $flagManager;

    /**
     * SyncStatusResource constructor.
     * @param Context $context
     * @param \Magento\Framework\FlagManager $flagManager
     * @param null $connectionName
     */
    public function __construct(Context $context, \Magento\Framework\FlagManager $flagManager, $connectionName = null)
    {
        parent::__construct($context, $connectionName);
        $this->flagManager = $flagManager;
    }

    protected function _construct()
    {
        $this->_init(
            'channel_amazon_sync_status',
            'id'
        );
    }

    public function recordSyncResult(
        AccountInterface $account,
        ?string $previousToken,
        ?string $nextToken,
        int $recordsFetched,
        string $status,
        ?string $notes = null
    ) {
        if (!in_array($status, self::$statuses, true)) {
            throw new \LogicException('Invalid status given for sync status record: ' . $status);
        }
        $this->getConnection()->insert(
            $this->getMainTable(),
            [
                'merchant_id' => (int)$account->getMerchantId(),
                'previous_token' => $previousToken,
                'next_token' => $nextToken,
                'records_fetched' => $recordsFetched,
                'status' => $status,
                'notes' => $notes
            ]
        );
        if ($status === self::STATUS_SUCCESS) {
            $this->flagManager->saveFlag($this->getFlagName($account), $nextToken);
        }
    }

    /**
     * @param AccountInterface $account
     * @return string|null
     */
    public function getNextToken(AccountInterface $account): ?string
    {
        return $this->flagManager->getFlagData($this->getFlagName($account));
    }

    private function getFlagName(AccountInterface $account): string
    {
        if (!$account->getUuid()) {
            throw new \RuntimeException('Cannot resolve next token flag name for the account without UUID');
        }
        return sprintf('asc_next_token_%s', (string)$account->getUuid());
    }

    public function getMostRecentStatuses(AccountInterface $account, int $limit = 10): array
    {
        $select = $this->getConnection()->select()
            ->from(
                $this->getMainTable(),
                [
                    'id',
                    'merchant_id',
                    'records_fetched',
                    'status',
                    'notes',
                    'previous_token',
                    'next_token',
                    'created_at'
                ]
            )
            ->where('merchant_id = ?', (int)$account->getMerchantId())
            ->order('id DESC')
            ->limit($limit);
        return $this->getConnection()->fetchAll($select);
    }

    public function cleanStatuses(?AccountInterface $account = null, \DateTimeInterface $before = null)
    {
        $adapter = $this->_resources->getConnection();
        $where = [];
        if ($account) {
            $where['merchant_id = ?'] = $account->getMerchantId();
        }
        if ($before) {
            $where['created_at < ?'] = $before->format('Y-m-d H:i:s');
        }
        $adapter->delete(
            $this->getMainTable(),
            $where
        );
    }

    public function deleteNextTokenForAccount(AccountInterface $account)
    {
        return $this->flagManager->deleteFlag($this->getFlagName($account));
    }
}
