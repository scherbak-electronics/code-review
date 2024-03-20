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
interface AttachmentLinkInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set attachment id.
     *
     * @param int $id
     * @return self
     */
    public function setId(int $id): self;

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
     * @return string|null
     */
    public function getSectionName(): ?string;

    /**
     * @param string $value
     * @return self
     */
    public function setSectionName(string $value): self;

    /**
     * @return string|null
     */
    public function getSectionDescription(): ?string;

    /**
     * @param string $value
     * @return self
     */
    public function setSectionDescription(string $value): self;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $value
     * @return self
     */
    public function setName(string $value): self;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string $value
     * @return self
     */
    public function setDescription(string $value): self;

    /**
     * @return string|null URL
     */
    public function getLink(): ?string;

    /**
     * @param string|null $value
     * @return self
     */
    public function setLink(?string $value): self;

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
     * Get human readable size
     *
     * @return int|null
     */
    public function getSize(): ?int;

    /**
     * Set human readable size
     *
     * @param string $value
     * @return self
     */
    public function setSize(string $value): self;

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
     * Count of downloads left (null for unlimited downloads)
     *
     * @return int|null
     */
    public function getDownloadsLeft(): ?int;

    /**
     * Set count of downloads left (null for unlimited downloads)
     *
     * @param int|null $value
     * @return self
     */
    public function setDownloadsLeft(?int $value): self;

    /**
     * @return bool|null
     */
    public function getIsHidden(): ?bool;

    /**
     * @param bool $value
     * @return self
     */
    public function setIsHidden(bool $value): self;
}
