<?php
namespace Aheadworks\QuickOrder\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\QuickOrder\Model\Config;
use Aheadworks\QuickOrder\Model\Service\CustomerService;

/**
 * Unit test for CustomerService
 *
 * @package Aheadworks\QuickOrder\Test\Unit\Model\Service
 */
class CustomerServiceTest extends TestCase
{
    /**
     * @var CustomerService
     */
    private $service;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->configMock = $this->createMock(Config::class);
        $this->service = $objectManager->getObject(
            CustomerService::class,
            [
                'config' => $this->configMock
            ]
        );
    }

    /**
     * test for isActiveForCustomerGroup method
     */
    public function testIsActiveForCustomerGroup()
    {
        $customerGroupId = 1;
        $websiteId = 2;
        $result = true;
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->with($websiteId)
            ->willReturn($result);
        $this->configMock->expects($this->once())
            ->method('isEnabledForCustomerGroup')
            ->with($customerGroupId, $websiteId)
            ->willReturn($result);

        $this->assertEquals($result, $this->service->isActiveForCustomerGroup($customerGroupId, $websiteId));
    }

    /**
     * test for isActiveForCustomerGroup method on false
     */
    public function testIsActiveForCustomerGroupOnFalse()
    {
        $customerGroupId = 1;
        $websiteId = 2;
        $result = false;
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->with($websiteId)
            ->willReturn($result);

        $this->assertEquals($result, $this->service->isActiveForCustomerGroup($customerGroupId, $websiteId));
    }
}
