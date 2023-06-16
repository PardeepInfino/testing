<?php
namespace Aheadworks\QuickOrder\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\GroupManagement;
use Aheadworks\QuickOrder\Model\Config;

/**
 * Unit test for Config
 *
 * @package Aheadworks\QuickOrder\Test\Unit\Model
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->model = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Test isEnabled method
     */
    public function testIsEnabled()
    {
        $expected = '1';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_ENABLED)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isEnabled());
    }

    /**
     * Test isEnabledForCustomerGroup method
     *
     * @dataProvider isEnabledForCustomerGroupDataProvider
     * @param int $customerGroupId
     * @param string $configValue
     * @param bool $result
     */
    public function testIsEnabledForCustomerGroup($customerGroupId, $configValue, $result)
    {
        $websiteId = 9999;
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Config::XML_PATH_GENERAL_CUSTOMER_GROUP_LIST_TO_BE_ENABLED,
                ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            )->willReturn($configValue);

        $this->assertEquals($result, $this->model->isEnabledForCustomerGroup($customerGroupId, $websiteId));
    }

    /**
     * Data provider for testIsEnabledForCustomerGroup method
     */
    public function isEnabledForCustomerGroupDataProvider()
    {
        return [
            [1, '1,2,3', true],
            [1, '4,2,3', false],
            [444, GroupManagement::CUST_GROUP_ALL, true],
            [5, '', false],
            [0, null, false]
        ];
    }

    /**
     * Test isAddToListButtonDisplayed method
     */
    public function testIsAddToListButtonDisplayed()
    {
        $expected = true;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_IS_ADD_TO_LIST_BUTTON_DISPLAYED)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isAddToListButtonDisplayed());
    }

    /**
     * Test isQtyInputDisplayed method
     */
    public function testIsQtyInputDisplayed()
    {
        $expected = false;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_IS_QTY_INPUT_DISPLAYED)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isQtyInputDisplayed());
    }
}
