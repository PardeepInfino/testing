<?php
namespace Aheadworks\QuickOrder\Test\Unit\Model\ProductList;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\QuickOrder\Model\ProductList\RequestConverter;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;

/**
 * Class RequestConverterTest
 *
 * @package Aheadworks\QuickOrder\Test\Unit\Model\ProductList
 */
class RequestConverterTest extends TestCase
{
    /**
     * @var RequestConverter
     */
    private $model;

    /**
     * Init mocks for tests
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(RequestConverter::class);
    }

    /**
     * Test for convertSkuListToRequestItems method
     *
     * @param string $list
     * @param array $result
     * @dataProvider skuListProvider
     */
    public function testConvertSkuListToRequestItems($list, $result)
    {
        $this->assertEquals($result, $this->model->convertSkuListToRequestItems($list));
    }

    /**
     * Sku list provider
     */
    public function skuListProvider()
    {
        return [
            ['sku1', [[ItemDataInterface::PRODUCT_SKU => 'sku1']]],
            [
                "sku1   \n  sku2  ",
                [
                    [ItemDataInterface::PRODUCT_SKU => 'sku1'],
                    [ItemDataInterface::PRODUCT_SKU => 'sku2']
                ]
            ],
            [
                "sku1            sku2  ",
                [
                    [ItemDataInterface::PRODUCT_SKU => 'sku1            sku2'],
                ]
            ],
        ];
    }

    /**
     * Test for convertCsvLinesToRequestItems method
     *
     * @param array $csvLines
     * @param array $result
     * @dataProvider scvLinesProvider
     */
    public function testConvertCsvLinesToRequestItems($csvLines, $result)
    {
        $this->assertEquals($result, $this->model->convertCsvLinesToRequestItems($csvLines));
    }

    /**
     * Csv lines provider
     */
    public function scvLinesProvider()
    {
        return [
            [
                [
                    ['sku1', '1']
                ],
                [
                    [
                        ItemDataInterface::PRODUCT_SKU => 'sku1',
                        ItemDataInterface::PRODUCT_QTY => '1'
                    ]
                ]
            ],
            [
                [
                    ['sku1']
                ],
                [
                    [
                        ItemDataInterface::PRODUCT_SKU => 'sku1',
                        ItemDataInterface::PRODUCT_QTY => '1'
                    ]
                ]
            ],
            [
                [
                    []
                ],
                []
            ],
            [
                [
                    ['       sku1      ', 100]
                ],
                [
                    [
                        ItemDataInterface::PRODUCT_SKU => 'sku1',
                        ItemDataInterface::PRODUCT_QTY => 100
                    ]
                ]
            ]
        ];
    }
}
