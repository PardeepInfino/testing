<?php
namespace Aheadworks\QuickOrder\Api\Data;

/**
 * Interface OperationResultInterface
 * @api
 */
interface OperationResultInterface
{
    /**
     * Operation result messages
     */
    const MESSAGES = 'message';
    /**#@-*/

    /**
     * Get result messages
     *
     * @return \Aheadworks\QuickOrder\Api\Data\OperationResultMessageInterface[]
     */
    public function getSuccessMessages();

    /**
     * Add success message to list
     *
     * @param string $title
     * @param string $text
     * @return $this
     */
    public function addSuccessMessage($title, $text);

    /**
     * Get result messages
     *
     * @return \Aheadworks\QuickOrder\Api\Data\OperationResultMessageInterface[]
     */
    public function getErrorMessages();

    /**
     * Add error message to list
     *
     * @param string $title
     * @param string $text
     * @return $this
     */
    public function addErrorMessage($title, $text);

    /**
     * Get messages
     *
     * @return \Aheadworks\QuickOrder\Api\Data\OperationResultMessageInterface[]
     */
    public function getMessages();

    /**
     * Set last added item key
     *
     * @param string $lastItemKey
     * @return $this
     */
    public function setLastAddedItemKey($lastItemKey);

    /**
     * Get last added item key
     *
     * @return string|null
     */
    public function getLastAddedItemKey();
}
