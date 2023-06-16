<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface TemplateInterface extends ExtensibleDataInterface
{
    public const MAIN_TABLE = 'amasty_alt_template';

    public const ID = 'id';
    public const ENABLED = 'enabled';
    public const TITLE = 'title';
    public const PRIORITY = 'priority';
    public const REPLACEMENT_LOGIC = 'replacement_logic';
    public const TEMPLATE = 'template';
    public const CONDITIONS_SERIALIZED = 'conditions_serialized';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return \Amasty\AltTagGenerator\Api\Data\TemplateInterface
     */
    public function setId($id);

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): void;

    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getReplacementLogic(): int;

    public function setReplacementLogic(int $replacementLogic): void;

    public function getTemplate(): string;

    public function setTemplate(string $template): void;

    public function getConditionsSerialized(): ?string;

    public function setConditionsSerialized(string $conditionsSerialized): void;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Amasty\AltTagGenerator\Api\Data\TemplateExtensionInterface|null
     */
    public function getExtensionAttributes(): ?TemplateExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \Amasty\AltTagGenerator\Api\Data\TemplateExtensionInterface $extensionAttributes
     *
     * @return void
     */
    public function setExtensionAttributes(TemplateExtensionInterface $extensionAttributes): void;
}
