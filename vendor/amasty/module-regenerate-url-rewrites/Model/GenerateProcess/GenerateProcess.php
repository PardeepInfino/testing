<?php

declare(strict_types=1);

namespace Amasty\RegenerateUrlRewrites\Model\GenerateProcess;

use Amasty\RegenerateUrlRewrites\Generator\Command\CommandResultInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * @method int|null getPid()
 * @method self setPid(int|null $pid)
 * @method self setStatus(string $status)
 * @method string getStatus()
 * @method string getFinished()
 * @method self setFinished(bool $finished)
 * @method CommandResultInterface|string|null getCommandResult()
 * @method self setCommandResult(CommandResultInterface|string|null $commandResult)
 * @method string getIdentity()
 * @method self setIdentity(string $identity)
 */
class GenerateProcess extends AbstractModel
{
    public const ID = 'id';
    public const PID = 'pid';
    public const STATUS = 'status';
    public const FINISHED = 'finished';
    public const COMMAND_RESULT = 'command_result';
    public const IDENTITY = 'identity';

    public const STATUS_STARTING = 'starting';
    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    /**
     * @return void
     */
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(ResourceModel\GenerateProcess::class);
        $this->setIdFieldName(self::ID);
    }

    /**
     * @param $message
     * @return void
     */
    public function addCriticalMessage($message): void
    {
        $this->addMessage(CommandResultInterface::MESSAGE_CRITICAL, $message);
    }

    /**
     * @param $message
     * @return void
     */
    public function addErrorMessage($message): void
    {
        $this->addMessage(CommandResultInterface::MESSAGE_ERROR, $message);
    }

    /**
     * @param $message
     * @return void
     */
    public function addWarningMessage($message): void
    {
        $this->addMessage(CommandResultInterface::MESSAGE_WARNING, $message);
    }

    /**
     * @param $message
     * @return void
     */
    public function addInfoMessage($message): void
    {
        $this->addMessage(CommandResultInterface::MESSAGE_INFO, $message);
    }

    /**
     * @param $message
     * @return void
     */
    public function addDebugMessage($message): void
    {
        $this->addMessage(CommandResultInterface::MESSAGE_DEBUG, $message);
    }

    /**
     * @param int $type
     * @param $message
     * @return void
     */
    public function addMessage(int $type, $message): void
    {
        $this->getCommandResult()->logMessage($type, $message);
    }
}
