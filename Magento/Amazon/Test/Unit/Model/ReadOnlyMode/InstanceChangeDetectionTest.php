<?php

namespace Magento\Amazon\Test\Unit\Model\ReadOnlyMode;

use Magento\Amazon\Model\ReadOnlyMode\InstanceChangeDetection;
use Magento\Framework\App\Config\Base;
use Magento\Framework\FlagManager;
use Magento\Framework\Serialize\Serializer\Base64Json;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use PHPUnit\Framework\TestCase;

class InstanceChangeDetectionTest extends TestCase
{
    /**
     * @var InstanceChangeDetection
     */
    private $instanceChangeDetection;

    private $mockedStores = [];

    protected function setUp(): void
    {
        $objectManager = new ObjectManagerHelper($this);

        $storeRepository = $this->getMockBuilder(StoreRepositoryInterface::class)->getMockForAbstractClass();
        $storeRepository->expects($this->any())->method('getList')->willReturnReference($this->mockedStores);

        $flags = [];
        $flagManager = $this->getMockBuilder(FlagManager::class)->disableOriginalConstructor()->getMock();
        $flagManager->expects($this->any())->method('saveFlag')->willReturnCallback(
            static function (string $flag, $value) use (&$flags) {
                $flags[$flag] = $value;
            }
        );
        $flagManager->expects($this->any())->method('getFlagData')->willReturnCallback(
            static function (string $flag) use (&$flags) {
                return $flags[$flag] ?? null;
            }
        );

        $storeRepository->expects($this->any())->method('getList')->willReturnReference($this->mockedStores);

        $this->instanceChangeDetection = $objectManager->getObject(
            InstanceChangeDetection::class,
            ['storeRepository' => $storeRepository, 'serializer' => new Base64Json(), 'flagManager' => $flagManager]
        );

        $this->mockedStores = [];
        $this->addStore('admin', 'http://localhost/', 'https://localhost/', true);
        $this->addStore('default', 'http://default.example.com/', 'https://default.example.com/', true);
    }

    private function addStore(string $code, string $baseUrl, string $secureBaseUrl, bool $isActive = true)
    {
        $mock = $this->getMockBuilder(StoreInterface::class)
            ->setMethods(['getCode', 'getBaseUrl', 'isActive'])
            ->getMockForAbstractClass();
        $mock->expects($this->any())->method('getCode')->willReturn($code);
        $mock->expects($this->any())->method('isActive')->willReturn($isActive);
        $mock->expects($this->any())->method('getBaseUrl')
            ->will(
                $this->returnValueMap(
                    [
                        [UrlInterface::URL_TYPE_WEB, true, $secureBaseUrl],
                        [UrlInterface::URL_TYPE_WEB, false, $baseUrl],
                    ]
                )
            );
        $this->mockedStores[$code] = $mock;
    }

    public function testInstanceIsNotNewOnBrandNewInstallation()
    {
        $this->assertFalse($this->instanceChangeDetection->isNewInstance());
    }

    public function testInstanceIsNotNewWhenStoreAdded()
    {
        $this->instanceChangeDetection->refreshPersistedToken();
        $this->addStore('new_store', 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->assertFalse($this->instanceChangeDetection->isNewInstance());
    }

    public function testInstanceIsNotNewWhenStoreRemoved()
    {
        $storeCode = 'store_to_be_deleted';
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->instanceChangeDetection->refreshPersistedToken();
        unset($this->mockedStores[$storeCode]);
        $this->assertFalse($this->instanceChangeDetection->isNewInstance());
    }

    public function testInstanceIsNotNewWhenOnConsecutiveCallsWithoutChanges()
    {
        $storeCode = 'untouched_store';
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->instanceChangeDetection->refreshPersistedToken();
        $this->assertFalse($this->instanceChangeDetection->isNewInstance());
        $this->assertFalse($this->instanceChangeDetection->isNewInstance());
    }

    public function testInstanceIsNewWhenWebsiteBaseUrlChanges()
    {
        $storeCode = 'store_with_base_url_to_change';
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->instanceChangeDetection->refreshPersistedToken();
        $this->addStore($storeCode, 'http://newstore2.example.com', 'https://newstore.example.com', true);
        $this->assertTrue($this->instanceChangeDetection->isNewInstance());
    }

    public function testInstanceIsNewWhenWebsiteSecureBaseUrlChanges()
    {
        $storeCode = 'store_with_base_url_to_change';
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->instanceChangeDetection->refreshPersistedToken();
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore3.example.com', true);
        $this->assertTrue($this->instanceChangeDetection->isNewInstance());
    }

    public function testDisablingStoreIsNotReportedAsInstanceChange()
    {
        $storeCode = 'store_to_be_disabled';
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->instanceChangeDetection->refreshPersistedToken();
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', false);
        $this->assertFalse($this->instanceChangeDetection->isNewInstance());
    }

    public function testUrlChangeInDisabledStoreIsNotReportedAsInstanceChange()
    {
        $storeCode = 'store_with_base_url_to_change';
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->instanceChangeDetection->refreshPersistedToken();
        $this->addStore($storeCode, 'http://newstore2.example.com', 'https://newstore2.example.com', true);
        $this->assertTrue($this->instanceChangeDetection->isNewInstance());
    }

    public function testUrlProtocolChangeIsNotConsideredAsInstanceChange()
    {
        $storeCode = 'store_with_base_url_to_change';
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->instanceChangeDetection->refreshPersistedToken();
        $this->addStore($storeCode, 'https://newstore.example.com', 'http://newstore.example.com', true);
        $this->assertFalse($this->instanceChangeDetection->isNewInstance());
    }

    public function testAddingTrailingUrlChangeIsNotConsideredAsInstanceChange()
    {
        $storeCode = 'store_with_base_url_to_change';
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->instanceChangeDetection->refreshPersistedToken();
        $this->addStore($storeCode, 'https://newstore.example.com/', 'http://newstore.example.com/', true);
        $this->assertFalse($this->instanceChangeDetection->isNewInstance());
    }

    public function testRemovingTrailingUrlChangeIsNotConsideredAsInstanceChange()
    {
        $storeCode = 'store_with_base_url_to_change';
        $this->addStore($storeCode, 'https://newstore.example.com/', 'http://newstore.example.com/', true);
        $this->instanceChangeDetection->refreshPersistedToken();
        $this->addStore($storeCode, 'http://newstore.example.com', 'https://newstore.example.com', true);
        $this->assertFalse($this->instanceChangeDetection->isNewInstance());
    }
}
