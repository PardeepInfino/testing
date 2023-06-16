<?php
namespace Aheadworks\QuickOrder\Model\Source\QuickOrder\OperationResult;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MessageType
 *
 * @package Aheadworks\QuickOrder\Model\Source\QuickOrder\OperationResult
 */
class MessageType implements OptionSourceInterface
{
    /**#@+
     * Message types
     */
    const SUCCESS = 'success';
    const ERROR = 'error';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SUCCESS, 'label' => __('Success')],
            ['value' => self::ERROR, 'label' => __('Error')]
        ];
    }
}
