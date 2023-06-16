<?php
namespace Aheadworks\QuickOrder\Test\Unit\Model\Service;

use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterfaceFactory;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterfaceFactory;
use Aheadworks\QuickOrder\Api\ProductListManagementInterface;
use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;
use Aheadworks\QuickOrder\Model\Exception\OperationException;
use Aheadworks\QuickOrder\Model\ProductList\Item\Processor as ItemProcessor;
use Aheadworks\QuickOrder\Model\Service\ProductListService;

/**
 * Unit test for ProductListService
 *
 * @package Aheadworks\QuickOrder\Test\Unit\Model\Service
 */
class ProductListServiceTest extends TestCase
{
    /**
     * @var ProductListService
     */
    private $service;

    /**
     * @var ProductListRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $listRepositoryMock;

    /**
     * @var OperationResultInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $operationResultFactoryMock;

    /**
     * @var ItemDataInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemDataFactoryMock;

    /**
     * @var ItemProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemProcessorMock;

    /**
     * Init mocks for tests
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->listRepositoryMock = $this->createMock(ProductListRepositoryInterface::class);
        $this->operationResultFactoryMock = $this->getMockBuilder(OperationResultInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemDataFactoryMock = $this->createMock(ItemDataInterfaceFactory::class);
        $this->itemProcessorMock = $this->createMock(ItemProcessor::class);
        $this->service = $objectManager->getObject(
            ProductListService::class,
            [
                'listRepository' => $this->listRepositoryMock,
                'operationResultFactory' => $this->operationResultFactoryMock,
                'itemDataFactory' => $this->itemDataFactoryMock,
                'itemProcessor' => $this->itemProcessorMock
            ]
        );
    }

    /**
     * Test removeItem method
     *
     * @throws \Exception
     */
    public function testRemoveItem()
    {
        $itemKey = '1234abcd';
        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);
        $this->itemProcessorMock->expects($this->once())
            ->method('remove')
            ->with($itemKey, $operationResultMock)
            ->willReturn(true);

        $this->assertEquals($operationResultMock, $this->service->removeItem($itemKey));
    }

    /**
     * Test updateItem method
     *
     * @throws \Exception
     */
    public function testUpdateItem()
    {
        $itemKey = '1234abcd';
        $storeId = 2;
        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);
        $requestItemMock = $this->createMock(ItemDataInterface::class);
        $this->itemProcessorMock->expects($this->once())
            ->method('update')
            ->with($itemKey, $requestItemMock, $operationResultMock)
            ->willReturn(true);

        $this->assertEquals($operationResultMock, $this->service->updateItem($itemKey, $requestItemMock, $storeId));
    }

    /**
     * Test removeAllItemsFromList method
     *
     * @throws \Exception
     */
    public function testRemoveAllItemsFromList()
    {
        $listId = '1';
        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);
        $listMock = $this->prepareProductList($listId);
        $this->listRepositoryMock->expects($this->once())
            ->method('save')
            ->with($listMock)
            ->willReturn($listMock);
        $operationResultMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with('', __('All items have been removed from the list'))
            ->willReturnSelf();

        $this->assertEquals($operationResultMock, $this->service->removeAllItemsFromList($listId));
    }

    /**
     * Test removeAllItemsFromList method on exception
     *
     * @throws \Exception
     */
    public function testRemoveAllItemsFromListOnException()
    {
        $listId = '1';
        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);
        $listMock = $this->prepareProductList($listId);
        $exceptionMessage = __('Cannot save the object');
        $this->listRepositoryMock->expects($this->once())
            ->method('save')
            ->with($listMock)
            ->willThrowException(new CouldNotSaveException($exceptionMessage));
        $operationResultMock->expects($this->once())
            ->method('addErrorMessage')
            ->with('', $exceptionMessage)
            ->willReturnSelf();

        $this->assertEquals($operationResultMock, $this->service->removeAllItemsFromList($listId));
    }

    /**
     * Prepare product list
     *
     * @param int $listId
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws \ReflectionException
     */
    private function prepareProductList($listId)
    {
        $listMock = $this->createMock(ProductListInterface::class);
        $listMock->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->listRepositoryMock->expects($this->once())
            ->method('get')
            ->with($listId)
            ->willReturn($listMock);

        return $listMock;
    }

    /**
     * Test addItemsToList method
     *
     * @throws \Exception
     */
    public function testAddItemsToList()
    {
        $listId = 1;

        $requestItemMock = $this->createMock(ItemDataInterface::class);
        $itemsData = [$requestItemMock];
        $sku = 'sku1';
        $requestItemMock->expects($this->exactly(2))
            ->method('getProductSku')
            ->willReturn($sku);
        $storeId = 2;
        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);

        $listMock = $this->getProductList($listId);

        $listItemMock = $this->createMock(ProductListItemInterface::class);
        $itemKey = '1234abcd';
        $listItemMock->expects($this->once())
            ->method('getItemKey')
            ->willReturn($itemKey);
        $this->itemProcessorMock->expects($this->once())
            ->method('create')
            ->with($requestItemMock, $storeId)
            ->willReturn($listItemMock);

        $operationResultMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('SKU: %1', $sku), __('The product has been added to the list'))
            ->willReturnSelf();
        $operationResultMock->expects($this->once())
            ->method('setLastAddedItemKey')
            ->with($itemKey)
            ->willReturnSelf();

        $this->saveProductList([$listItemMock], $listMock);
        $this->assertEquals($operationResultMock, $this->service->addItemsToList($listId, $itemsData, $storeId));
    }

    /**
     * Test addItemsToList method on exception
     *
     * @throws \Exception
     */
    public function testAddItemsToListOnException()
    {
        $listId = 1;

        $requestItemMock = $this->createMock(ItemDataInterface::class);
        $itemsData = [$requestItemMock];
        $sku = 'sku1';
        $requestItemMock->expects($this->exactly(2))
            ->method('getProductSku')
            ->willReturn($sku);
        $storeId = 2;
        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->operationResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($operationResultMock);

        $listMock = $this->getProductList($listId);

        $exceptionMessage = __('The product is not available');
        $this->itemProcessorMock->expects($this->once())
            ->method('create')
            ->with($requestItemMock, $storeId)
            ->willThrowException(new OperationException($exceptionMessage));

        $operationResultMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('SKU: %1', $sku), $exceptionMessage)
            ->willReturnSelf();

        $this->saveProductList([], $listMock);
        $this->assertEquals($operationResultMock, $this->service->addItemsToList($listId, $itemsData, $storeId));
    }

    /**
     * Get product list
     *
     * @param int $listId
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getProductList($listId)
    {
        $listMock = $this->createMock(ProductListInterface::class);
        $listMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);
        $this->listRepositoryMock->expects($this->once())
            ->method('get')
            ->with($listId)
            ->willReturn($listMock);

        return $listMock;
    }

    /**
     * Save product list
     *
     * @param \PHPUnit\Framework\MockObject\MockObject $items
     * @param \PHPUnit\Framework\MockObject\MockObject $listMock
     */
    private function saveProductList($items, $listMock)
    {
        $listMock->expects($this->once())
            ->method('setItems')
            ->with($items)
            ->willReturnSelf();
        $this->listRepositoryMock->expects($this->once())
            ->method('save')
            ->with($listMock)
            ->willReturnSelf();
    }
}
