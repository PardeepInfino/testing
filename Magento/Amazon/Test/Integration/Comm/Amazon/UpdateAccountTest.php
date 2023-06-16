<?php

declare(strict_types=1);

namespace Magento\Amazon\Test\Integration\Comm\Amazon;

use Magento\Amazon\Comm\Amazon\UpdateAccount;
use Magento\Amazon\Model\Amazon\AccountRepository;
use Magento\Amazon\Model\ApiClient;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class UpdateAccountTest extends TestCase
{
    /**
     * @var UpdateAccount
     */
    private $updateAccount;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ApiClient
     */
    private $apiClientMock;
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->objectManager;
        $this->apiClientMock = $this->getMockBuilder(ApiClient::class)->disableOriginalConstructor()->getMock();
        $this->updateAccount = $this->objectManager->create(
            \Magento\Amazon\Comm\Amazon\UpdateAccount::class,
            ['apiClient' => $this->apiClientMock]
        );
        $this->accountRepository = $this->objectManager->get(AccountRepository::class);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testUpdateAccountHandlesEmptyResponse()
    {
        $this->apiClientMock->expects($this->once())->method('getMerchant')->willReturn([]);
        $account = $this->accountRepository->getAccountByName('mage-test');
        $this->updateAccount->update($account);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     * @dataProvider statusAndAuthenticationStatusDataProvider
     * @param string $status
     * @param string $authenticationStatus
     * @param int $expectedStatus
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testUpdateAccountHandlesStatusAndAuthenticationStatusChange(
        string $status,
        string $authenticationStatus,
        int $expectedStatus
    ) {
        $this->apiClientMock->expects($this->once())->method('getMerchant')->willReturn(
            [
                'status' => $status,
                'authentication_status' => $authenticationStatus,
            ]
        );
        $account = $this->accountRepository->getAccountByName('mage-test');
        $this->updateAccount->update($account);
        $this->assertEquals($authenticationStatus, $account->getAuthenticationStatus());
        $this->assertEquals($expectedStatus, $account->getIsActive());
    }

    public function statusAndAuthenticationStatusDataProvider(): array
    {
        return [
            ['status' => 'active', 'authentication_status' => 'authenticated', 'expectedStatus' => 1],
            ['status' => 'active', 'authentication_status' => 'pending_authentication', 'expectedStatus' => 1],
            ['status' => 'inactive', 'authentication_status' => 'authenticated', 'expectedStatus' => 0],
            ['status' => 'inactive', 'authentication_status' => 'pending_authentication', 'expectedStatus' => 0],
        ];
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     * @dataProvider statusAndAuthenticationStatusDataProvider
     * @param string $status
     * @param string $authenticationStatus
     * @param int $expectedStatus
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testAccountChangesArePersisted(
        string $status,
        string $authenticationStatus,
        int $expectedStatus
    ) {
        $this->apiClientMock->expects($this->once())->method('getMerchant')->willReturn(
            [
                'status' => $status,
                'authentication_status' => $authenticationStatus,
            ]
        );
        $initialAccount = $this->accountRepository->getAccountByName('mage-test');
        $this->updateAccount->update($initialAccount);
        $refreshedAccount = $this->accountRepository->getAccountByName('mage-test');
        $this->assertEquals($authenticationStatus, $refreshedAccount->getAuthenticationStatus());
        $this->assertEquals($expectedStatus, $refreshedAccount->getIsActive());
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDataFieldsAreChanged()
    {
        // cannot really update the name — fixture (and rollback) relies on it
        $name = 'mage-test';
        $sellerId = 'brand-new cool seller id';
        $countryCode = 'CZ';
        $email = 'cool-email@example.com';
        $url = 'https://mybeautifulstore.example.com';
        $this->apiClientMock->expects($this->once())->method('getMerchant')->willReturn(
            [
                'name' => $name,
                'seller_id' => $sellerId,
                'countryCode' => $countryCode,
                'email' => $email,
                'url' => $url,
            ]
        );
        $initialAccount = $this->accountRepository->getAccountByName('mage-test');
        $this->updateAccount->update($initialAccount);

        $refreshedAccount = $this->accountRepository->getAccountByName($name);
        $this->assertEquals($name, $refreshedAccount->getName());
        $this->assertEquals($sellerId, $refreshedAccount->getSellerId());
        $this->assertEquals($countryCode, $refreshedAccount->getCountryCode());
        $this->assertEquals($email, $refreshedAccount->getEmail());
        $this->assertEquals($url, $refreshedAccount->getBaseUrl());
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testNullsDoesNotOverwriteStoredValues()
    {
        $initialAccount = $this->accountRepository->getAccountByName('mage-test');

        $name = $initialAccount->getName();
        $sellerId = $initialAccount->getSellerId();
        $countryCode = $initialAccount->getCountryCode();
        $email = $initialAccount->getEmail();
        $url = $initialAccount->getBaseUrl();
        $this->apiClientMock->expects($this->once())->method('getMerchant')->willReturn(
            [
                'name' => null,
                'seller_id' => null,
                'countryCode' => null,
                'email' => null,
                'url' => null,
            ]
        );
        $this->updateAccount->update($initialAccount);

        $refreshedAccount = $this->accountRepository->getAccountByName($name);
        $this->assertEquals($name, $refreshedAccount->getName());
        $this->assertEquals($sellerId, $refreshedAccount->getSellerId());
        $this->assertEquals($countryCode, $refreshedAccount->getCountryCode());
        $this->assertEquals($email, $refreshedAccount->getEmail());
        $this->assertEquals($url, $refreshedAccount->getBaseUrl());
    }
}
