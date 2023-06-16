<?php

namespace Magento\Amazon\Test\Unit\Service;

use Magento\Amazon\Model\ReadOnlyMode\InstanceChangeDetection;
use Magento\Amazon\Model\ReadOnlyMode\ReadOnlyConfiguration;
use Magento\Amazon\Service\ReadOnlyMode;
use Magento\Amazon\Service\ReadOnlyModeException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReadOnlyModeTest extends TestCase
{
    /**
     * @var MockObject|InstanceChangeDetection
     */
    private $instanceChangeDetection;
    /**
     * @var MockObject|ScopeConfigInterface
     */
    private $readOnlyConfiguration;
    /**
     * @var ReadOnlyMode
     */
    private $readOnlyMode;

    protected function setUp(): void
    {
        $objectManager = new ObjectManagerHelper($this);
        $this->instanceChangeDetection = $this->getMockBuilder(InstanceChangeDetection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->readOnlyConfiguration = $this->getMockBuilder(ReadOnlyConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->readOnlyMode = $objectManager->getObject(
            ReadOnlyMode::class,
            [
                'readOnlyConfiguration' => $this->readOnlyConfiguration,
                'instanceChangeDetection' => $this->instanceChangeDetection
            ]
        );
    }

    public function testReadOnlyModeEnabledThroughConfig()
    {
        $this->readOnlyConfiguration->expects($this->any())->method('isEnabled')->willReturn(true);
        $this->instanceChangeDetection->expects($this->never())->method('isNewInstance');
        $this->assertTrue($this->readOnlyMode->isEnabled());
    }

    public function testReadOnlyModeDisabledThroughConfigAndNoNewInstanceDetected()
    {
                $this->readOnlyConfiguration->expects($this->any())->method('isEnabled')->willReturn(false);
        $this->instanceChangeDetection->expects($this->once())->method('isNewInstance')->willReturn(false);
        $this->assertFalse($this->readOnlyMode->isEnabled());
    }

    public function testReadOnlyModeDisabledThroughConfigAndNewInstanceDetected()
    {
                $this->readOnlyConfiguration->expects($this->any())->method('isEnabled')->willReturn(false);
        $this->instanceChangeDetection->expects($this->once())->method('isNewInstance')->willReturn(true);
        $this->assertTrue($this->readOnlyMode->isEnabled());
    }

    /**
     * @param bool $readOnlyMode
     * @param bool $isNewInstanceValue
     * @dataProvider assertThrowsExceptionDataProvider
     */
    public function testAssertThrowsException(bool $readOnlyMode, bool $isNewInstanceValue)
    {
        $this->expectException(ReadOnlyModeException::class);
        $message = 'my read only exception';
        $this->expectExceptionMessage($message);
        $this->readOnlyConfiguration->expects($this->any())->method('isEnabled')->willReturn($readOnlyMode);
        $this->instanceChangeDetection->expects($this->any())->method('isNewInstance')->willReturn($isNewInstanceValue);
        $this->assertTrue($this->readOnlyMode->isEnabled());
        $this->readOnlyMode->assertNotInReadOnlyMode($message);
    }

    public function assertThrowsExceptionDataProvider(): array
    {
        return [
            'enabled-in-config-on-the-same-instance' => [true, false],
            'enabled-in-config-on-the-new-instance' => [true, true],
            'disabled-in-config-on-the-new-instance' => [false, true],
        ];
    }

    /**
     * @param bool $readOnlyMode
     * @param bool $isNewInstanceValue
     * @dataProvider assertDoesNotThrowExceptionDataProvider
     */
    public function testAssertDoesNotThrowException(bool $readOnlyMode, bool $isNewInstanceValue)
    {
        $message = 'my read only exception';
        $this->readOnlyConfiguration->expects($this->any())->method('isEnabled')->willReturn($readOnlyMode);
        $this->instanceChangeDetection->expects($this->any())->method('isNewInstance')->willReturn($isNewInstanceValue);
        $this->assertFalse($this->readOnlyMode->isEnabled());
        $this->readOnlyMode->assertNotInReadOnlyMode($message);
    }

    public function assertDoesNotThrowExceptionDataProvider(): array
    {
        return [
            'disabled-in-config-on-the-same-instance' => [false, false],
        ];
    }
}
