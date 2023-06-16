<?php

declare(strict_types=1);

namespace Magento\Amazon\Test\Integration\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Comm\Amazon\UpdateHandler\OrderItem;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\CollectionFactory;

class OrderItemTest extends BaseTest
{
    /**
     * @var Order
     */
    private $updateHandler;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->updateHandler = $this->objectManager->create(
            OrderItem::class,
            ['chunkedHandler' => $this->chunkedHandler]
        );
        $this->collectionFactory = $this->objectManager->create(CollectionFactory::class);
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account.php
     */
    public function testAbleToSaveOrderItemsWithIncompleteFieldsInTheSameBatch()
    {
        $this->markTestIncomplete('This test is not ready yet');
        $ordersLogs = json_decode($this->getTestFileContents('orders.json'), true);
        $this->logger->expects($this->never())->method('error');
        $this->logger->expects($this->never())->method('critical');
        $account = $this->getAccountByName('mage-test');
        $this->updateHandler->handle($ordersLogs, $account);
        $collection = $this->getCollection();
        $storedOrders = $collection
            ->addFieldToFilter('order_id', array_column($ordersLogs, 'order_id'))
            ->addFieldToFilter('merchant_id', $account->getMerchantId())
            ->getData();
        $this->assertCount(count($ordersLogs), $storedOrders);
    }

    private function getCollection(): \Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\Collection
    {
        return $this->collectionFactory->create();
    }
}