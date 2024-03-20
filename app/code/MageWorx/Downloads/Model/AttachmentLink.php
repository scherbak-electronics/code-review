<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Model;

use MageWorx\Downloads\Api\Data\AttachmentLinkInterface;

class AttachmentLink extends \Magento\Framework\Api\AbstractExtensibleObject implements AttachmentLinkInterface
{
    const ID                  = 'id';
    const SECTION_ID          = 'section_id';
    const SECTION_NAME        = 'section_name';
    const SECTION_DESCRIPTION = 'section_description';
    const NAME                = 'name';
    const DESCRIPTION         = 'description';
    const LINK                = 'link';
    const ICON_LINK           = 'icon_link';
    const TYPE                = 'type';
    const FILETYPE            = 'filetype';
    const SIZE                = 'size';
    const DOWNLOADS           = 'downloads';
    const DOWNLOADS_LEFT      = 'downloads_left';
    const IS_HIDDEN           = 'is_hidden';

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        $id = $this->_get(self::ID);

        if (!$id) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @param int $id
     * @return AttachmentLinkInterface
     */
    public function setId(int $id): AttachmentLinkInterface
    {
        return $this->setData(self::SECTION_ID, $id);
    }

    /**
     * @return int|null
     */
    public function getSectionId(): ?int
    {
        $id = $this->_get(self::SECTION_ID);

        if (!$id) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @param int $value
     * @return AttachmentLinkInterface
     */
    public function setSectionId(int $value): AttachmentLinkInterface
    {
        return $this->setData(self::SECTION_ID, $value);
    }

    /**
     * @return string|null
     */
    public function getSectionName(): ?string
    {
        return (string)$this->_get(self::SECTION_NAME);
    }

    /**
     * @param string $value
     * @return AttachmentLinkInterface
     */
    public function setSectionName(string $value): AttachmentLinkInterface
    {
        return $this->setData(self::SECTION_NAME, $value);
    }

    /**
     * @return string|null
     */
    public function getSectionDescription(): ?string
    {
        return (string)$this->_get(self::SECTION_DESCRIPTION);
    }

    /**
     * @param string $value
     * @return AttachmentLinkInterface
     */
    public function setSectionDescription(string $value): AttachmentLinkInterface
    {
        return $this->setData(self::SECTION_DESCRIPTION, $value);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return (string)$this->_get(self::NAME);
    }

    /**
     * @param string $value
     * @return AttachmentLinkInterface
     */
    public function setName(string $value): AttachmentLinkInterface
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return (string)$this->_get(self::DESCRIPTION);
    }


    /**
     * @param string $value
     * @return AttachmentLinkInterface
     */
    public function setDescription(string $value): AttachmentLinkInterface
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * Return link
     *
     * @return string|null
     */
    public function getLink(): ?string
    {
        return (string)$this->_get(self::LINK);
    }

    /**
     * Set link
     *
     * @param string|null $value
     * @return AttachmentLinkInterface
     */
    public function setLink(?string $value): AttachmentLinkInterface
    {
        return $this->setData(self::LINK, $value);
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return (string)$this->_get(self::TYPE);
    }

    /**
     * @param string $value
     * @return AttachmentLinkInterface
     */
    public function setType(string $value): AttachmentLinkInterface
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * @return string|null
     */
    public function getFiletype(): ?string
    {
        return (string)$this->_get(self::FILETYPE);
    }

    /**
     * @param string $value
     * @return AttachmentLinkInterface
     */
    public function setFiletype(string $value): AttachmentLinkInterface
    {
        return $this->setData(self::FILETYPE, $value);
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return (int)$this->_get(self::SIZE);
    }

    /**
     * @param string $value
     * @return AttachmentLinkInterface
     */
    public function setSize(string $value): AttachmentLinkInterface
    {
        return $this->setData(self::SIZE, $value);
    }

    /**
     * Count of downloads
     *
     * @return int|null
     */
    public function getDownloads(): ?int
    {
        return (int)$this->_get(self::DOWNLOADS);
    }

    /**
     * Set count of downloads
     *
     * @param int|null $value
     * @return AttachmentLinkInterface
     */
    public function setDownloads(?int $value): AttachmentLinkInterface
    {
        return $this->setData(self::DOWNLOADS, $value);
    }

    /**
     * Count of downloads left
     *
     * @return int|null
     */
    public function getDownloadsLeft(): ?int
    {
        return (int)$this->_get(self::DOWNLOADS_LEFT);
    }

    /**
     * Set count of downloads left
     *
     * @param int|null $value
     * @return AttachmentLinkInterface
     */
    public function setDownloadsLeft(?int $value): AttachmentLinkInterface
    {
        return $this->setData(self::DOWNLOADS_LEFT, $value);
    }

    /**
     * @return bool|null
     */
    public function getIsHidden(): ?bool
    {
        return (bool)$this->_get(self::IS_HIDDEN);
    }

    /**
     * @param bool $value
     * @return AttachmentLinkInterface
     */
    public function setIsHidden(bool $value): AttachmentLinkInterface
    {
        return $this->setData(self::IS_HIDDEN, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @return \MageWorx\Downloads\Api\Data\AttachmentLinkExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->AttachmentLinkInterfaceExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \MageWorx\Downloads\Api\Data\AttachmentLinkExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\Downloads\Api\Data\AttachmentLinkExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
