<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Api\Data;

interface SectionLocaleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get storeId
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Return the label for the store
     *
     * @return string
     */
    public function getStoreName(): string;

    /**
     * Set the label for the store
     *
     * @param string $storeName
     * @return $this
     */
    public function setStoreName($storeName);

    /**
     * Return the label for the store
     *
     * @return string
     */
    public function getStoreDescription(): string;

    /**
     * Set the description for the store
     *
     * @param string $storeDescription
     * @return $this
     */
    public function setStoreDescription($storeDescription);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \MageWorx\Downloads\Api\Data\SectionLocaleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \MageWorx\Downloads\Api\Data\SectionLocaleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\Downloads\Api\Data\SectionLocaleExtensionInterface $extensionAttributes
    );
}
