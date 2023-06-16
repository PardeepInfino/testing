<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Comm\Amazon;

use Magento\Amazon\Api\AccountManagementInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ApiClient;
use Magento\Amazon\Model\ResourceModel\Amazon\Action as ActionResourceModel;
use Magento\Amazon\Service\ReadOnlyMode;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class for pushing commands to host
 */
class PushUpdates
{
    /** Max commands to be processed per push */
    const CHUNK_SIZE = 500;

    /**
     * @var ActionResourceModel $actionResourceModel
     */
    private $actionResourceModel;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var ApiClient
     */
    private $apiClient;
    /**
     * @var ReadOnlyMode
     */
    private $readOnlyMode;

    /**
     * @param ActionResourceModel $actionResourceModel
     * @param AccountManagementInterface $accountManagement
     * @param ApiClient $apiClient
     * @param ReadOnlyMode $readOnlyMode
     */
    public function __construct(
        ActionResourceModel $actionResourceModel,
        AccountManagementInterface $accountManagement,
        ApiClient $apiClient,
        ReadOnlyMode $readOnlyMode
    ) {
        $this->actionResourceModel = $actionResourceModel;
        $this->accountManagement = $accountManagement;
        $this->apiClient = $apiClient;
        $this->readOnlyMode = $readOnlyMode;
    }

    /**
     * Prepare commands and push them to the server.
     *
     * @param AccountInterface $account
     * @param int $merchantId
     * @throws LocalizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    private function processCommands(AccountInterface $account, int $merchantId)
    {
        $commandList = $this->actionResourceModel->getByMerchantId($merchantId);
        $commandListChunks = array_chunk($commandList, self::CHUNK_SIZE);

        foreach ($commandListChunks as $commandListChunk) {
            $commands = [];
            $commandIds = [];
            foreach ($commandListChunk as $commandData) {
                $commands[] = [
                    'name' => $commandData['command'],
                    'body' => $commandData['command_body'],
                ];
                $commandIds[] = $commandData['id'];
            }

            if (!empty($commands)) {
                $this->apiClient->pushCommands($account, $commands);
                $this->actionResourceModel->deleteByIds($commandIds);
            }
        }
    }

    /**
     * @param AccountInterface $account
     * @throws LocalizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function push(AccountInterface $account): void
    {
        if ($this->readOnlyMode->isEnabled()) {
            return;
        }
        $merchantId = $account->getMerchantId();
        $accountReadyToPushCommands = $this->accountManagement->isAccountReadyToPushCommands($account);

        if ($accountReadyToPushCommands) {
            $this->processCommands($account, $merchantId);
        }
    }
}
