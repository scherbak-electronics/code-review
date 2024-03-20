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
interface AttachmentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID              = 'attachment_id';
    const SECTION_ID      = 'section_id';
    const NAME            = 'name';
    const IS_ATTACH       = 'is_attach';
    const FILENAME        = 'filename';
    const URL             = 'url';
    const TYPE            = 'type';
    const FILETYPE        = 'filetype';
    const SIZE            = 'size';
    const DESCRIPTION     = 'description';
    const DOWNLOADS       = 'downloads';
    const DOWNLOADS_LIMIT = 'downloads_limit';
    const FILE_CONTENT    = 'file_content';
    const DATE_MODIFIED   = 'date_modified';
    const DATE_ADDED      = 'date_added';
    const IS_ACTIVE       = 'is_active';
    const CUSTOMER_GROUPS = 'customer_group_ids';
    const PRODUCTS        = 'product_ids';
    const STORE_LOCALES   = 'store_locales';
    const STORES          = 'store_ids';

    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set category id.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int|null
     */
    public function getSectionId(): ?int;

    /**
     * @param int $value
     * @return self
     */
    public function setSectionId(int $value): self;

    /**
     * @return bool|null
     */
    public function getIsAttach(): ?bool;

    /**
     * @param bool $value
     * @return self
     */
    public function setIsAttach(bool $value): self;

    /**
     * Return file path or null when type is 'link'
     *
     * @return string|null relative file path
     */
    public function getFilename(): ?string;

    /**
     * Set file path or null when type is 'link'
     *
     * @param string|null $value
     * @return self
     */
    public function setFilename(?string $value): self;

    /**
     * Return link url or null when type is 'file'
     *
     * @return string|null
     */
    public function getUrl(): ?string;

    /**
     * Set URL
     *
     * @param string|null $value
     * @return self
     */
    public function setUrl(?string $value): self;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string $value
     * @return self
     */
    public function setType(string $value): self;

    /**
     * @return string|null
     */
    public function getFiletype(): ?string;

    /**
     * @param string $value
     * @return self
     */
    public function setFiletype(string $value): self;

    /**
     * @return int|null
     */
    public function getSize(): ?int;

    /**
     * @param int $value
     * @return self
     */
    public function setSize(int $value): self;

    /**
     * Count of downloads
     *
     * @return int|null
     */
    public function getDownloads(): ?int;

    /**
     * Set count of downloads
     *
     * @param int|null $value
     * @return self
     */
    public function setDownloads(?int $value): self;

    /**
     * Count of possible downloads
     * 0 for unlimited downloads
     *
     * @return int|null
     */
    public function getDownloadsLimit(): ?int;

    /**
     * Set count of possible downloads
     * 0 for unlimited downloads
     *
     * @param int $value
     * @return self
     */
    public function setDownloadsLimit(int $value): self;

    /**
     * @return string|null
     */
    public function getDateModified(): ?string;

    /**
     * @param string $value
     * @return self
     */
    public function setDateModified(string $value): self;

    /**
     * @return string|null
     */
    public function getDateAdded(): ?string;

    /**
     * @param string $value
     * @return self
     */
    public function setDateAdded(string $value): self;

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
     * Get a list of stores the attachment assigned to
     *
     * @return int[]|null
     */
    public function getStoreIds(): ?array;

    /**
     * Set the stores the attachment assigned to
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds);

    /**
     * Get display locales
     *
     * @return \MageWorx\Downloads\Api\Data\AttachmentLocaleInterface[]|null
     */
    public function getStoreLocales(): ?array;

    /**
     * Set display locales
     *
     * @param \MageWorx\Downloads\Api\Data\AttachmentLocaleInterface[]|null $storeLocales
     * @return self
     */
    public function setStoreLocales(array $storeLocales = null): self;

    /**
     * Get ids of customer groups
     *
     * @return int[]
     */
    public function getCustomerGroupIds();

    /**
     * Set the customer groups
     *
     * @param int[] $customerGroupIds
     * @return self
     */
    public function setCustomerGroupIds(array $customerGroupIds): self;

    /**
     * Get ids of products
     *
     * @return int[]
     */
    public function getProductIds();

    /**
     * Set the products
     *
     * @param int[]|null $productIds
     * @return self
     */
    public function setProductIds(?array $productIds): self;

    /**
     * Return file content
     *
     * @return \MageWorx\Downloads\Api\Data\File\ContentInterface|null
     */
    public function getAttachmentFileContent();

    /**
     * Set file content
     *
     * @param File\ContentInterface|null $linkFileContent
     * @return $this
     */
    public function setAttachmentFileContent(\MageWorx\Downloads\Api\Data\File\ContentInterface $linkFileContent = null
    );

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \MageWorx\Downloads\Api\Data\AttachmentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \MageWorx\Downloads\Api\Data\AttachmentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\Downloads\Api\Data\AttachmentExtensionInterface $extensionAttributes
    );
}
