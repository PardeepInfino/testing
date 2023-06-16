<?php

namespace Amasty\RegenerateUrlRewrites\Generator\Command;

interface CommandResultInterface
{
    public const MESSAGE_CRITICAL = 50;
    public const MESSAGE_ERROR = 40;
    public const MESSAGE_WARNING = 30;
    public const MESSAGE_INFO = 20;
    public const MESSAGE_DEBUG = 10;

    /**
     * @return bool
     */
    public function isFailed(): bool;

    /**
     * @param bool $failed
     * @return void
     */
    public function setFailed(bool $failed = false): void;

    /**
     * @param int $type
     * @param $message
     * @return void
     */
    public function logMessage(int $type, $message): void;

    /**
     * @return array
     */
    public function getMessages(): array;

    /**
     * @param array $messages
     * @return void
     */
    public function setMessages(array $messages): void;

    /**
     * @return void
     */
    public function clearMessages(): void;

    /**
     * @return int
     */
    public function getTotalRecords(): int;

    /**
     * @param int $records
     * @return void
     */
    public function setTotalRecords(int $records): void;

    /**
     * @return int
     */
    public function getRecordsProcessed(): int;

    /**
     * @param int $records
     * @return void
     */
    public function setRecordsProcessed(int $records): void;

    /**
     * @return false|string|null
     */
    public function serialize();

    /**
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized): void;
}
