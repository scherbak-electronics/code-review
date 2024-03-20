<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Api\Data\File;

interface ContentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Retrieve data (base64 encoded content)
     *
     * @return string
     */
    public function getFileData();

    /**
     * Set data (base64 encoded content)
     *
     * @param string $fileData
     * @return $this
     */
    public function setFileData($fileData);

    /**
     * Retrieve file name
     *
     * @return string
     */
    public function getName();

    /**
     * Set file name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \MageWorx\Downloads\Api\Data\File\ContentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \MageWorx\Downloads\Api\Data\File\ContentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\Downloads\Api\Data\File\ContentExtensionInterface $extensionAttributes
    );
}