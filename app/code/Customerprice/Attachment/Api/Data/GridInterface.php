<?php
namespace Customerprice\Attachment\Api\Data;

interface GridInterface
{
    const ENTITY_ID = 'entity_id';
    const NAME = 'name';
    const CUSTOMER_TYPE = 'customer_type';
    const PRICELIST_TYPE = 'pricelist_type';
    const STATUS = 'status';
    const FILE = 'file';
    const POSITION = 'position';

    public function getEntityId();

    public function setEntityId($entityId);

    public function getName();

    public function setName($name);

    public function getCustomerType();

    public function setCustomerType($customerType);

    public function getPricelistType();

    public function SetPricelistType($pricelistType);

    public function getStatus();

    public function setStatus($status);

    public function getFile();

    public function setFile($file);

    public function getPosition();

    public function setPosition($position);
}