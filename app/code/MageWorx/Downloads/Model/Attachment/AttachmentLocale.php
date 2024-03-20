<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Model\Attachment;


class AttachmentLocale extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \MageWorx\Downloads\Api\Data\AttachmentLocaleInterface
{
    const KEY_STORE_ID          = 'store_id';
    const KEY_STORE_NAME        = 'store_name';
    const KEY_STORE_DESCRIPTION = 'store_description';

    /**
     * Get storeId
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(self::KEY_STORE_ID);
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::KEY_STORE_ID, $storeId);
    }

    /**
     * Return the label for the store
     *
     * @return string
     */
    public function getStoreName(): string
    {
        return (string)$this->getData(self::KEY_STORE_NAME);
    }

    /**
     * Set the label for the store
     *
     * @param string $storeName
     * @return $this
     */
    public function setStoreName($storeName)
    {
        return $this->setData(self::KEY_STORE_NAME, $storeName);
    }

    /**
     * Return the description for the store
     *
     * @return string
     */
    public function getStoreDescription(): string
    {
        return (string)$this->getData(self::KEY_STORE_DESCRIPTION);
    }

    /**
     * Set the description for the store
     *
     * @param string $storeDescription
     * @return $this
     */
    public function setStoreDescription($storeDescription)
    {
        return $this->setData(self::KEY_STORE_DESCRIPTION, $storeDescription);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magento\Framework\Api\ExtensionAttributesInterface|\MageWorx\Downloads\Api\Data\AttachmentLocaleExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \MageWorx\Downloads\Api\Data\AttachmentLocaleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\Downloads\Api\Data\AttachmentLocaleExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
