<?php
namespace Aheadworks\QuickOrder\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;
use Aheadworks\QuickOrder\Model\Service\CartService;
use Aheadworks\QuickOrder\Model\Quote\Product\DataProcessor;
use Aheadworks\QuickOrder\Model\Product\AvailabilityChecker;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterfaceFactory;

/**
 * Unit test for CartService
 *
 * @package Aheadworks\QuickOrder\Test\Unit\Model\Service
 */
class CartServiceTest extends TestCase
{
    /**
     * @var CartService
     */
    private $service;

    /**
     * @var CartRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRepositoryMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var ProductListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $listMock;

    /**
     * @var Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteMock;

    /**
     * @var OperationResultInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $operationResultFactoryMock;

    /**
     * @var ProductListRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $listRepositoryMock;

    /**
     * @var DataProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProcessorMock;

    /**
     * @var AvailabilityChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $availabilityCheckerMock;

    /**
     * @var array
     */
    private $testData = [
        'list_id' => 2,
        'cart_id' => 3,
        'website_id' => 1,
        'store_id' => 2
    ];

    /**
     * Init mocks for tests
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->cartRepositoryMock = $this->createMock(CartRepositoryInterface::class);
        $this->listRepositoryMock = $this->createMock(ProductListRepositoryInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->operationResultFactoryMock = $this->getMockBuilder(OperationResultInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataProcessorMock = $this->createMock(DataProcessor::class);
        $this->availabilityCheckerMock = $this->createMock(AvailabilityChecker::class);
        $this->service = $objectManager->getObject(
            CartService::class,
            [
                'cartRepository' => $this->cartRepositoryMock,
                'listRepository' => $this->listRepositoryMock,
                'storeManager' => $this->storeManagerMock,
                'operationResultFactory' => $this->operationResultFactoryMock,
                'dataProcessor' => $this->dataProcessorMock,
                'availabilityChecker' => $this->availabilityCheckerMock,

            ]
        );
    }

    /**
     * Test for addListToCart method with list containing no items
     */
    public function testAddListToCartWithNoItems()
    {
        $items = [];

        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);

        $this->prepareItemsBefore($items);
        $this->prepareWebsiteId();
        $this->prepareItemsAfter($items);
        $this->assertEquals(
            $operationResultMock,
            $this->service->addListToCart($this->testData['list_id'], $this->testData['cart_id'])
        );
    }

    /**
     * Test for addListToCart method with success items
     */
    public function testAddListToCartWithSuccess()
    {
        $itemSku = 'sku1';
        $itemMock = $this->prepareItem($itemSku);
        $items = [$itemMock];
        $this->prepareItemsBefore($items);
        $productMock = $this->createMock(Product::class);
        $this->dataProcessorMock->expects($this->once())
            ->method('getProduct')
            ->with($itemMock)
            ->willReturn($productMock);

        $websiteId = $this->prepareWebsiteId();
        $this->availabilityCheckerMock->expects($this->once())
            ->method('isAvailableForSale')
            ->with($productMock, $itemMock, $websiteId)
            ->willReturn(true);

        $quoteItemMock = $this->createMock(QuoteItem::class);
        $this->addProduct($productMock, $itemMock, $quoteItemMock);

        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);
        $operationResultMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('SKU: %1', $itemSku), __('The product has been added to cart'))
            ->willReturnSelf();

        $this->prepareItemsAfter([]);
        $this->assertEquals(
            $operationResultMock,
            $this->service->addListToCart($this->testData['list_id'], $this->testData['cart_id'])
        );
    }

    /**
     * Test for addListToCart method with not available items
     */
    public function testAddListToCartWithNotAvailable()
    {
        $itemSku = 'sku2';
        $itemMock = $this->prepareItem($itemSku);
        $items = [$itemMock];
        $this->prepareItemsBefore($items);
        $productMock = $this->createMock(Product::class);
        $this->dataProcessorMock->expects($this->once())
            ->method('getProduct')
            ->with($itemMock)
            ->willReturn($productMock);

        $websiteId = $this->prepareWebsiteId();
        $this->availabilityCheckerMock->expects($this->once())
            ->method('isAvailableForSale')
            ->with($productMock, $itemMock, $websiteId)
            ->willReturn(false);

        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);
        $operationResultMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('SKU: %1', $itemSku), __('The product is not available'))
            ->willReturnSelf();

        $this->prepareItemsAfter($items);
        $this->assertEquals(
            $operationResultMock,
            $this->service->addListToCart($this->testData['list_id'], $this->testData['cart_id'])
        );
    }

    /**
     * Test for addListToCart method with error
     */
    public function testAddListToCartWithError()
    {
        $itemSku = 'sku3';
        $itemMock = $this->prepareItem($itemSku);
        $items = [$itemMock];
        $this->prepareItemsBefore($items);
        $productMock = $this->createMock(Product::class);
        $this->dataProcessorMock->expects($this->once())
            ->method('getProduct')
            ->with($itemMock)
            ->willReturn($productMock);

        $websiteId = $this->prepareWebsiteId();
        $this->availabilityCheckerMock->expects($this->once())
            ->method('isAvailableForSale')
            ->with($productMock, $itemMock, $websiteId)
            ->willReturn(true);

        $result = 'Error';
        $this->addProduct($productMock, $itemMock, $result);

        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);
        $operationResultMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('SKU: %1', $itemSku), $result)
            ->willReturnSelf();

        $this->prepareItemsAfter($items);
        $this->assertEquals(
            $operationResultMock,
            $this->service->addListToCart($this->testData['list_id'], $this->testData['cart_id'])
        );
    }

    /**
     * Test for addListToCart method on exception
     */
    public function testAddListToCartOnException()
    {
        $itemSku = 'sku3';
        $itemMock = $this->prepareItem($itemSku);
        $items = [$itemMock];
        $this->prepareItemsBefore($items);

        $this->prepareWebsiteId();
        $exceptionMessage = 'Error has been thrown';
        $this->dataProcessorMock->expects($this->once())
            ->method('getProduct')
            ->with($itemMock)
            ->willThrowException(new \Exception($exceptionMessage));

        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);
        $operationResultMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('SKU: %1', $itemSku), $exceptionMessage)
            ->willReturnSelf();

        $this->prepareItemsAfter($items);
        $this->assertEquals(
            $operationResultMock,
            $this->service->addListToCart($this->testData['list_id'], $this->testData['cart_id'])
        );
    }

    /**
     * Prepare website ID
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $quoteMock
     * @return int
     * @throws \ReflectionException
     */
    private function prepareWebsiteId()
    {
        $websiteId = $this->testData['website_id'];
        $storeId = $this->testData['store_id'];
        $storeMock = $this->createMock(StoreInterface::class);
        $this->quoteMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        return $websiteId;
    }

    /**
     * Prepare items before
     *
     * @param \PHPUnit_Framework_MockObject_MockObject[] $items
     */
    private function prepareItemsBefore($items)
    {
        $listId = $this->testData['list_id'];
        $cartId = $this->testData['cart_id'];
        $this->listMock = $this->createMock(ProductListInterface::class);
        $this->listMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
        $this->listRepositoryMock->expects($this->once())
            ->method('get')
            ->with($listId)
            ->willReturn($this->listMock);
        $this->quoteMock = $this->createPartialMock(
            Quote::class,
            ['collectTotals', 'getStoreId', 'addProduct', 'getShippingAddress']
        );
        $this->cartRepositoryMock->expects($this->once())
            ->method('get')
            ->with($cartId)
            ->willReturn($this->quoteMock);
    }

    /**
     * Prepare items after
     *
     * @param \PHPUnit_Framework_MockObject_MockObject[] $items
     */
    private function prepareItemsAfter($items)
    {
        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturnSelf();
        $this->cartRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->quoteMock);
        $this->listMock->expects($this->once())
            ->method('setItems')
            ->with($items)
            ->willReturnSelf();
        $this->listRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->listMock)
            ->willReturn($this->listMock);
    }

    /**
     * Prepare product list item
     *
     * @param string $sku
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function prepareItem($sku)
    {
        $itemMock = $this->createMock(ProductListItemInterface::class);
        $itemMock->expects($this->once())
            ->method('getProductSku')
            ->willReturn($sku);

        return $itemMock;
    }

    /**
     * Add product
     *
     * @param \PHPUnit\Framework\MockObject\MockObject $productMock
     * @param \PHPUnit\Framework\MockObject\MockObject $itemMock
     * @param $result
     */
    private function addProduct($productMock, $itemMock, $result)
    {
        $buyRequest = ['some data'];
        $this->dataProcessorMock->expects($this->once())
            ->method('getBuyRequest')
            ->with($itemMock)
            ->willReturn($buyRequest);

        $this->quoteMock->expects($this->once())
            ->method('addProduct')
            ->with($productMock, $buyRequest)
            ->willReturn($result);
    }
}
