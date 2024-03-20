<?php

namespace Customerprice\Attachment\Model;

use Customerprice\Attachment\Api\Data\GridInterface;

class Grid extends \Magento\Framework\Model\AbstractModel implements GridInterface
{
    const CACHE_TAG = 'customerprice_attachment_records';

    protected $_cacheTag = 'customerprice_attachment_records';
    protected $_eventPrefix = 'customerprice_attachment_records';

    protected function _construct()
    {
        $this->_init('Customerprice\Attachment\Model\ResourceModel\Grid');
    }

    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getCustomerType()
    {
        return $this->getData(self::CUSTOMER_TYPE);
    }

    public function setCustomerType($customerType)
    {
        return $this->setData(self::CUSTOMER_TYPE, $customerType);
    }

    public function getPricelistType()
    {
        return $this->getData(self::PRICELIST_TYPE);
    }

    public function SetPricelistType($pricelistType)
    {
        return $this->setData(self::PRICELIST_TYPE, $pricelistType);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getFile()
    {
        return $this->getData(self::FILE);
    }

    public function setFile($file)
    {
        return $this->setData(self::FILE, $file);
    }

    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

}
