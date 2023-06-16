<?php
namespace Aheadworks\QuickOrder\Api\Data;

/**
 * Interface OperationResultMessageInterface
 * @api
 */
interface OperationResultMessageInterface
{
    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const TYPE = 'type';
    const TITLE = 'title';
    const TEXT = 'text';
    /**#@-*/

    /**
     * Get message type
     *
     * @return string
     */
    public function getType();

    /**
     * Get message title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get message text
     *
     * @return string
     */
    public function getText();
}
