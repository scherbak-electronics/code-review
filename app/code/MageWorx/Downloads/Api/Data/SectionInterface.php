<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Api\Data;

/**
 * @api
 */
interface SectionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID            = 'section_id';
    const NAME          = 'name';
    const DESCRIPTION   = 'description';
    const STORE_LOCALES = 'store_locales';
    const IS_ACTIVE     = 'is_active';

    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    const DEFAULT_ID = 1;

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return self
     */
    public function setId($id);

    /**
     * Get display locales
     *
     * @return \MageWorx\Downloads\Api\Data\SectionLocaleInterface[]|null
     */
    public function getStoreLocales(): ?array;

    /**
     * Set display locales
     *
     * @param \MageWorx\Downloads\Api\Data\SectionLocaleInterface[]|null $storeLocales
     * @return self
     */
    public function setStoreLocales(array $storeLocales = null): self;

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool;

    /**
     * @param bool $value
     * @return self
     */
    public function setIsActive(bool $value): self;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \MageWorx\Downloads\Api\Data\SectionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \MageWorx\Downloads\Api\Data\SectionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\MageWorx\Downloads\Api\Data\SectionExtensionInterface $extensionAttributes);
}
