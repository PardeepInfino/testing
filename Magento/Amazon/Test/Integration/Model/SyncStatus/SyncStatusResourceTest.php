<?php

declare(strict_types=1);

namespace Magento\Amazon\Test\Integration\Model\SyncStatus;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\Amazon\AccountRepository;
use Magento\Amazon\Model\SyncStatus\SyncStatusResource;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SyncStatusResourceTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var SyncStatusResource
     */
    private $syncStatusResource;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->syncStatusResource = $this->objectManager->get(SyncStatusResource::class);
        $this->accountRepository = $this->objectManager->get(AccountRepository::class);
    }

    private function assertStatusMatches(
        array $actualStatus,
        AccountInterface $account,
        $previousToken,
        $nextToken,
        $recordsFetched,
        $status,
        $notes
    ) {
        $this->assertEquals($account->getMerchantId(), $actualStatus['merchant_id'], 'Merchant Id does not match');
        $this->assertEquals($previousToken, $actualStatus['previous_token'], 'Previous token does not match');
        $this->assertEquals($nextToken, $actualStatus['next_token'], 'Next token does not match');
        $this->assertEquals($recordsFetched, $actualStatus['records_fetched'], 'Fetched records count does not match');
        $this->assertEquals($status, $actualStatus['status'], 'Status does not match');
        $this->assertEquals($notes, $actualStatus['notes'], 'Notes does not match');
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testStoreSuccessfulResult()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $previousToken = 'my previous successful test token';
        $nextToken = 'my next successful test token';
        $recordsFetched = 321;
        $status = SyncStatusResource::STATUS_SUCCESS;
        $notes = 'my successful notes';
        $this->syncStatusResource->recordSyncResult(
            $account,
            $previousToken,
            $nextToken,
            $recordsFetched,
            $status,
            $notes
        );
        $statuses = $this->syncStatusResource->getMostRecentStatuses($account, 10);
        $this->assertCount(1, $statuses);
        $this->assertStatusMatches(
            $statuses[0],
            $account,
            $previousToken,
            $nextToken,
            $recordsFetched,
            $status,
            $notes
        );
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testStoreApiError()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $previousToken = 'my previous api error test token';
        $nextToken = 'my next api error test token';
        $recordsFetched = 321;
        $status = SyncStatusResource::STATUS_API_ERROR;
        $notes = 'my api error test notes';
        $this->syncStatusResource->recordSyncResult(
            $account,
            $previousToken,
            $nextToken,
            $recordsFetched,
            $status,
            $notes
        );
        $statuses = $this->syncStatusResource->getMostRecentStatuses($account, 10);
        $this->assertCount(1, $statuses);
        $this->assertStatusMatches(
            $statuses[0],
            $account,
            $previousToken,
            $nextToken,
            $recordsFetched,
            $status,
            $notes
        );
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testStoreUnknownError()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $previousToken = 'my previous unknown error test token';
        $nextToken = 'my next unknown error test token';
        $recordsFetched = 321;
        $status = SyncStatusResource::STATUS_API_ERROR;
        $notes = 'my unknown error test notes';
        $this->syncStatusResource->recordSyncResult(
            $account,
            $previousToken,
            $nextToken,
            $recordsFetched,
            $status,
            $notes
        );
        $statuses = $this->syncStatusResource->getMostRecentStatuses($account, 10);
        $this->assertCount(1, $statuses);
        $this->assertStatusMatches(
            $statuses[0],
            $account,
            $previousToken,
            $nextToken,
            $recordsFetched,
            $status,
            $notes
        );
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testStoreUnexpectedStatusThrowsException()
    {
        $this->expectException(\LogicException::class);
        $account = $this->accountRepository->getAccountByName('mage-test');
        $status = 'poor-software-design-error';
        $this->syncStatusResource->recordSyncResult(
            $account,
            'my previous unknown status exception test token',
            'my next unknown status exception test token',
            321,
            $status,
            'my unknown status exception test notes'
        );
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testRetrieveNullNextTokenForTheFirstTime()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $retrievedNextToken = $this->syncStatusResource->getNextToken($account);
        $this->assertNull($retrievedNextToken);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testRetrieveLastSuccessfulNextToken()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $previousToken = 'my previous successful test token';
        $nextToken = 'my next successful test token';
        $recordsFetched = 321;
        $status = SyncStatusResource::STATUS_SUCCESS;
        $notes = 'my successful notes';
        $this->syncStatusResource->recordSyncResult(
            $account,
            $previousToken,
            $nextToken,
            $recordsFetched,
            $status,
            $notes
        );
        $retrievedNextToken = $this->syncStatusResource->getNextToken($account);
        $this->assertEquals($nextToken, $retrievedNextToken);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testReturnsMostRecentNextToken()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $previousToken = 'my previous successful test token';
        $firstNextToken = 'my first next token';
        $secondNextToken = 'my second next token';
        $recordsFetched = 321;
        $status = SyncStatusResource::STATUS_SUCCESS;
        $notes = 'my successful notes';
        $this->syncStatusResource->recordSyncResult(
            $account,
            $previousToken,
            $firstNextToken,
            $recordsFetched,
            $status,
            $notes
        );
        $this->syncStatusResource->recordSyncResult(
            $account,
            $firstNextToken,
            $secondNextToken,
            $recordsFetched,
            $status,
            $notes
        );
        $retrievedNextToken = $this->syncStatusResource->getNextToken($account);
        $this->assertEquals($secondNextToken, $retrievedNextToken);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testApiErrorDoesNotOverwriteNextToken()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $previousToken = 'my previous successful test token';
        $firstNextToken = 'my first next token';
        $secondNextToken = 'my second next token';
        $recordsFetched = 321;
        $notes = 'my successful notes';
        $this->syncStatusResource->recordSyncResult(
            $account,
            $previousToken,
            $firstNextToken,
            $recordsFetched,
            SyncStatusResource::STATUS_SUCCESS,
            $notes
        );
        $this->syncStatusResource->recordSyncResult(
            $account,
            $firstNextToken,
            $secondNextToken,
            $recordsFetched,
            SyncStatusResource::STATUS_API_ERROR,
            $notes
        );
        $retrievedNextToken = $this->syncStatusResource->getNextToken($account);
        $this->assertEquals($firstNextToken, $retrievedNextToken);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testUnknownErrorDoesNotOverwriteNextToken()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $previousToken = 'my previous successful test token';
        $firstNextToken = 'my first next token';
        $secondNextToken = 'my second next token';
        $recordsFetched = 321;
        $notes = 'my successful notes';
        $this->syncStatusResource->recordSyncResult(
            $account,
            $previousToken,
            $firstNextToken,
            $recordsFetched,
            SyncStatusResource::STATUS_SUCCESS,
            $notes
        );
        $this->syncStatusResource->recordSyncResult(
            $account,
            $firstNextToken,
            $secondNextToken,
            $recordsFetched,
            SyncStatusResource::STATUS_UNKNOWN_ERROR,
            $notes
        );
        $retrievedNextToken = $this->syncStatusResource->getNextToken($account);
        $this->assertEquals($firstNextToken, $retrievedNextToken);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testNextTokenStillRetrievableAfterCleaningStatuses()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $previousToken = 'my previous successful test token';
        $firstNextToken = 'my first next token';
        $secondNextToken = 'my second next token';
        $recordsFetched = 321;
        $notes = 'my successful notes';
        $this->syncStatusResource->recordSyncResult(
            $account,
            $previousToken,
            $firstNextToken,
            $recordsFetched,
            SyncStatusResource::STATUS_SUCCESS,
            $notes
        );
        $this->syncStatusResource->recordSyncResult(
            $account,
            $firstNextToken,
            $secondNextToken,
            $recordsFetched,
            SyncStatusResource::STATUS_SUCCESS,
            $notes
        );
        $this->assertCount(2, $this->syncStatusResource->getMostRecentStatuses($account, 10));
        $this->assertEquals($secondNextToken, $this->syncStatusResource->getNextToken($account));
        $this->syncStatusResource->cleanStatuses($account);
        $this->assertCount(0, $this->syncStatusResource->getMostRecentStatuses($account, 10));
        $this->assertEquals($secondNextToken, $this->syncStatusResource->getNextToken($account));
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testNextTokenIsEmptyAfterExplicitDelete()
    {
        $account = $this->accountRepository->getAccountByName('mage-test');
        $previousToken = 'my previous successful test token';
        $nextToken = 'my first next token';
        $recordsFetched = 321;
        $notes = 'my successful notes';
        $this->syncStatusResource->recordSyncResult(
            $account,
            $previousToken,
            $nextToken,
            $recordsFetched,
            SyncStatusResource::STATUS_SUCCESS,
            $notes
        );
        $this->assertEquals($nextToken, $this->syncStatusResource->getNextToken($account));
        $this->syncStatusResource->deleteNextTokenForAccount($account);
        $this->assertNull($this->syncStatusResource->getNextToken($account));
    }
}
