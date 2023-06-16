<?php
namespace Aheadworks\QuickOrderGraphQl\Test\Unit\Model\Resolver;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Aheadworks\QuickOrderGraphQl\Model\Resolver\Config as ConfigResolver;
use Aheadworks\QuickOrder\Model\Config as QuickOrderConfig;

/**
 * Class ConfigTest
 *
 * @package Aheadworks\QuickOrderGraphQl\Test\Unit\Model\Resolver
 */
class ConfigTest extends TestCase
{
    /**
     * @var ConfigResolver
     */
    private $model;

    /**
     * @var QuickOrderConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quickOrderConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->quickOrderConfigMock = $this->getMockBuilder(QuickOrderConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'isEnabled',
                    'isAddToListButtonDisplayed',
                    'isQtyInputDisplayed',
                ]
            )
            ->getMock();

        $this->model = $objectManager->getObject(
            ConfigResolver::class,
            [
                'quickOrderConfig' => $this->quickOrderConfigMock,
            ]
        );
    }

    /**
     * Test resolve method
     *
     * @param array $args
     * @param int|null $websiteId
     * @dataProvider resolveDataProvider
     * @throws \ReflectionException
     */
    public function testResolve($args, $websiteId)
    {
        $isEnabled = true;
        $configData = [
            'is_quick_order_enabled' => $isEnabled,
            'is_add_to_list_button_displayed' => $isEnabled,
            'is_qty_input_displayed' => $isEnabled
        ];

        $fieldMock = $this->createMock(Field::class);
        $contextMock = $this->createMock(ContextInterface::class);
        $resolveInfoMock = $this->createMock(ResolveInfo::class);

        $this->quickOrderConfigMock->expects($this->once())
            ->method('isEnabled')
            ->with($websiteId)
            ->willReturn($isEnabled);
        $this->quickOrderConfigMock->expects($this->once())
            ->method('isAddToListButtonDisplayed')
            ->with($websiteId)
            ->willReturn($isEnabled);
        $this->quickOrderConfigMock->expects($this->once())
            ->method('isQtyInputDisplayed')
            ->with($websiteId)
            ->willReturn($isEnabled);

        $this->assertEquals($configData, $this->model->resolve(
            $fieldMock,
            $contextMock,
            $resolveInfoMock,
            [],
            $args
        ));
    }

    /**
     * @return array
     */
    public function resolveDataProvider()
    {
        return [
            'General' => [
                'args' => [
                    'websiteId' => 1,
                ],
                'websiteId' => 1,
            ],
            'Missing website ID' => [
                'args' => [
                    'some_value' => 1,
                ],
                'websiteId' => null,
            ],
            'Empty' => [
                'args' => [],
                'websiteId' => null,
            ],
        ];
    }
}
