<?php

namespace Magento\Amazon\Test\Integration\Comm\Amazon;

use Magento\Amazon\Comm\Amazon\PullLogs;
use Magento\Amazon\Model\Amazon\Account;
use Magento\Amazon\Model\Amazon\AccountRepository;
use Magento\Amazon\Model\ApiClient;
use Magento\Amazon\Model\SyncStatus\SyncStatusResource;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class PullLogsTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var MockObject|ApiClient
     */
    private $apiClientMock;
    /**
     * @var PullLogs
     */
    private $pullLogs;
    /**
     * @var \Magento\Amazon\Model\SyncStatus\SyncStatusResource
     */
    private $syncStatusResource;
    /**
     * @var \Magento\Amazon\Model\Logs\LogsResource
     */
    private $logsResource;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->apiClientMock = $this->getMockBuilder(ApiClient::class)->disableOriginalConstructor()->getMock();
        $this->pullLogs = $this->objectManager->create(
            \Magento\Amazon\Comm\Amazon\PullLogs::class,
            ['apiClient' => $this->apiClientMock]
        );
        $this->syncStatusResource = $this->objectManager->get(
            \Magento\Amazon\Model\SyncStatus\SyncStatusResource::class
        );
        $this->logsResource = $this->objectManager->get(\Magento\Amazon\Model\Logs\LogsResource::class);
        $this->accountRepository = $this->objectManager->get(AccountRepository::class);
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getTestFileContents(string $fileName): string
    {
        /** @var string $content */
        $content = file_get_contents(__DIR__ . '/../../../_files/' . $fileName);

        return $content;
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testLogsPulledFromApi()
    {
        $this->apiClientMock->expects($this->once())->method('fetchLogs')->willReturn([]);
        $account = $this->accountRepository->getAccountByName('mage-test');
        $this->pullLogs->pull($account);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testNoStatusRecordedWhenNoLogsPulled()
    {
        $this->apiClientMock->expects($this->once())
            ->method('fetchLogs')
            ->willReturn(['logs' => [], 'lastLogToken' => null]);
        $account = $this->accountRepository->getAccountByName('mage-test');
        $this->pullLogs->pull($account);
        $statuses = $this->syncStatusResource->getMostRecentStatuses($account, 1);
        $this->assertEmpty($statuses);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testReceivedLogsWereStoredInDatabase()
    {
        $lastLogToken = 'shiny-last-log-token';
        $account = $this->accountRepository->getAccountByName('mage-test');
        $logData = [
            'id' => random_int(1, 4000000),
            'identifier' => 'log-id',
            'type' => 'Order',
            'action' => 'Insert',
            'log' => '{}',
        ];
        $this->apiClientMock->expects($this->at(0))
            ->method('fetchLogs')
            ->with($account, null)
            ->willReturn(['logs' => [$logData], 'lastLogToken' => $lastLogToken]);
        $this->pullLogs->pull($account);
        $logs = $this->logsResource->getLogs($account);
        $this->assertCount(1, $logs);
        $this->assertLogsEqual(array_merge($logData, ['merchant_id' => $account->getMerchantId()]), reset($logs));
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testSuccessfulSyncStatusHasBeenRecordAndContainsCorrectLastToken()
    {
        $lastLogToken = 'shiny-last-log-token';
        $account = $this->accountRepository->getAccountByName('mage-test');
        $logData = [
            'id' => random_int(1, 4000000),
            'identifier' => 'log-id',
            'type' => 'Order',
            'action' => 'Insert',
            'log' => '{}',
        ];
        $this->apiClientMock->expects($this->at(0))
            ->method('fetchLogs')
            ->with($account, null)
            ->willReturn(['logs' => [$logData], 'lastLogToken' => $lastLogToken]);
        $this->pullLogs->pull($account);
        $syncStatuses = $this->syncStatusResource->getMostRecentStatuses($account, 3);
        $this->assertCount(1, $syncStatuses);
        $syncStatus = reset($syncStatuses);
        $this->assertEquals(SyncStatusResource::STATUS_SUCCESS, $syncStatus['status']);
        $this->assertEquals($lastLogToken, $syncStatus['next_token']);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testSecondCallWasMadeToApiWithPreviouslyReturnedToken()
    {
        $lastLogToken = 'shiny-last-log-token';
        $account = $this->accountRepository->getAccountByName('mage-test');
        $logData = [
            'id' => random_int(1, 4000000),
            'identifier' => 'log-id',
            'type' => 'Order',
            'action' => 'Insert',
            'log' => '{}',
        ];
        $this->apiClientMock->expects($this->at(0))
            ->method('fetchLogs')
            ->with($account, null)
            ->willReturn(['logs' => [$logData], 'lastLogToken' => $lastLogToken]);
        $this->apiClientMock->expects($this->at(1))
            ->method('fetchLogs')
            ->with($account, $lastLogToken)
            ->willReturn(['logs' => [], 'lastLogToken' => $lastLogToken]);
        $this->pullLogs->pull($account);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testUpdatedLogOverwritesPreviousLogData()
    {
        $lastLogToken = 'shiny-last-log-token';
        $account = $this->accountRepository->getAccountByName('mage-test');
        $logData = [
            'id' => random_int(1, 4000000),
            'identifier' => 'log-id',
            'type' => 'Order',
            'action' => 'Insert',
            'log' => '{}',
        ];
        $updatedLogData = array_merge($logData, ['log' => '{"name": "hello"}']);
        $this->apiClientMock->expects($this->at(0))
            ->method('fetchLogs')
            ->with($account, null)
            ->willReturn(['logs' => [$logData], 'lastLogToken' => $lastLogToken]);
        $this->apiClientMock->expects($this->at(1))
            ->method('fetchLogs')
            ->with($account, $lastLogToken)
            ->willReturn(['logs' => [$updatedLogData], 'lastLogToken' => $lastLogToken]);
        $this->pullLogs->pull($account);
        $logs = $this->logsResource->getLogs($account);
        $this->assertCount(1, $logs);
        $this->assertLogsEqual(array_merge($updatedLogData, ['merchant_id' => $account->getMerchantId()]), reset($logs));
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testUpdatedActionInsertsNewLogs()
    {
        $lastLogToken = 'shiny-last-log-token';
        $account = $this->accountRepository->getAccountByName('mage-test');
        $firstLog = [
            'id' => random_int(1, 4000000),
            'identifier' => 'log-id',
            'type' => 'Order',
            'action' => 'Insert',
            'log' => '{}',
        ];
        $secondLog = array_merge($firstLog, ['action' => 'Remove']);
        $this->apiClientMock->expects($this->at(0))
            ->method('fetchLogs')
            ->with($account, null)
            ->willReturn(['logs' => [$firstLog], 'lastLogToken' => $lastLogToken]);
        $this->apiClientMock->expects($this->at(1))
            ->method('fetchLogs')
            ->with($account, $lastLogToken)
            ->willReturn(['logs' => [$secondLog], 'lastLogToken' => $lastLogToken]);
        $this->pullLogs->pull($account);
        $logs = array_values($this->logsResource->getLogs($account));
        $this->assertCount(2, $logs);
        $this->assertLogsEqual(array_merge($firstLog, ['merchant_id' => $account->getMerchantId()]), $logs[0]);
        $this->assertLogsEqual(array_merge($secondLog, ['merchant_id' => $account->getMerchantId()]), $logs[1]);
    }

    private function assertLogsEqual(array $expectedLogData, $actualLogData)
    {
        if (array_key_exists('id', $expectedLogData)) {
            $this->assertArrayHasKey('external_id', $actualLogData);
            $this->assertEquals($expectedLogData['id'], $actualLogData['external_id']);
        }
        if (array_key_exists('identifier', $expectedLogData)) {
            $this->assertArrayHasKey('identifier', $actualLogData);
            $this->assertEquals($expectedLogData['identifier'], $actualLogData['identifier']);
        }
        if (array_key_exists('type', $expectedLogData)) {
            $this->assertArrayHasKey('type', $actualLogData);
            $this->assertEquals($expectedLogData['type'], $actualLogData['type']);
        }
        if (array_key_exists('action', $expectedLogData)) {
            $this->assertArrayHasKey('action', $actualLogData);
            $this->assertEquals($expectedLogData['action'], $actualLogData['action']);
        }
        if (array_key_exists('log', $expectedLogData)) {
            $this->assertArrayHasKey('log', $actualLogData);
            $this->assertEquals($expectedLogData['log'], $actualLogData['log']);
        }
        if (array_key_exists('merchant_id', $expectedLogData)) {
            $this->assertArrayHasKey('merchant_id', $actualLogData);
            $this->assertEquals($expectedLogData['merchant_id'], $actualLogData['merchant_id']);
        }
    }
}
