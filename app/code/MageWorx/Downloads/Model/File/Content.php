<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\File;

use MageWorx\Downloads\Api\Data\File\ContentInterface;

/**
 * @codeCoverageIgnore
 */
class Content extends \Magento\Framework\Model\AbstractExtensibleModel implements ContentInterface
{
    const DATA = 'file_data';
    const NAME = 'name';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getFileData()
    {
        return $this->getData(self::DATA);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set data (base64 encoded content)
     *
     * @param string $fileData
     * @return $this
     * @codeCoverageIgnore
     */
    public function setFileData($fileData)
    {
        return $this->setData(self::DATA, $fileData);
    }

    /**
     * Set file name
     *
     * @param string $name
     * @return $this
     * @codeCoverageIgnore
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magento\Framework\Api\ExtensionAttributesInterface|\MageWorx\Downloads\Api\Data\File\ContentExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \MageWorx\Downloads\Api\Data\File\ContentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\Downloads\Api\Data\File\ContentExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
