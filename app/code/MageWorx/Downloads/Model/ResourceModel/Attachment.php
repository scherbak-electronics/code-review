<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Catalog\Model\Product;
use MageWorx\Downloads\Model\Attachment\Product as AttachmentProduct;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime as LibDateTime;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Event\ManagerInterface;

class Attachment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const PRODUCT_RELATION_TABLE = 'mageworx_downloads_product_relation';

    const STORE_RELATION_TABLE = 'mageworx_downloads_attachment_store';

    const CUSTOMER_GROUP_RELATION_TABLE = 'mageworx_downloads_attachment_customer_group';

    const ATTACHMENT_LOCALE_RELATION_TABLE = 'mageworx_downloads_attachment_locale';

    /**
     * @var \MageWorx\Downloads\Model\Attachment\Product
     */
    protected $attachmentProduct;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param Context $context
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param LibDateTime $dateTime
     * @param ManagerInterface $eventManager
     * @param AttachmentProduct $attachmentProduct
     * @param \Magento\Framework\Stdlib\StringUtils $string
     */
    public function __construct(
        Context $context,
        DateTime $date,
        StoreManagerInterface $storeManager,
        LibDateTime $dateTime,
        ManagerInterface $eventManager,
        AttachmentProduct $attachmentProduct,
        \Magento\Framework\Stdlib\StringUtils $string
    ) {
        parent::__construct($context);

        $this->date              = $date;
        $this->storeManager      = $storeManager;
        $this->dateTime          = $dateTime;
        $this->eventManager      = $eventManager;
        $this->attachmentProduct = $attachmentProduct;
        $this->string            = $string;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_downloads_attachment', 'attachment_id');
    }

    /**
     * Before save callback
     *
     * @param AbstractModel|\MageWorx\Downloads\Model\Attachment $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setDateModified($this->date->gmtDate());
        if ($object->isObjectNew()) {
            $object->setDateAdded($this->date->gmtDate());
        }

        if (null === $object->getContentType()) {
            $object->setIsActive(false);
        }

        return parent::_beforeSave($object);
    }


    /**
     * @param \MageWorx\Downloads\Model\Attachment $object
     * @return array
     */
    public function getProducts(\MageWorx\Downloads\Model\Attachment $object): array
    {
        $select = $this->getConnection()->select()
                       ->from(
                           $this->getTable(self::PRODUCT_RELATION_TABLE),
                           ['product_id']
                       )
                       ->where(
                           'attachment_id = ?',
                           (int)$object->getAttachmentId()
                       );

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getAttachmentIdsByProductId($productId)
    {
        $select = $this->getConnection()->select()
                       ->from(
                           $this->getTable(self::PRODUCT_RELATION_TABLE),
                           ['attachment_id']
                       )
                       ->where(
                           'product_id = ?',
                           (int)$productId
                       );

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param Product $product
     * @param array|null $attachments
     * @return $this
     */
    public function saveAttachmentProductRelation(Product $product, $attachments)
    {
        $productId = $product->getId();
        if ($attachments === null) {
            return $this;
        }

        $preAttachmentModels = $this->attachmentProduct->getSelectedAttachments($product);
        $preAttachmentModels = is_array($preAttachmentModels) ? $preAttachmentModels : [];

        $preAttachments = [];
        foreach ($preAttachmentModels as $attachment) {
            /** @var \MageWorx\Downloads\Model\Attachment $attachment */
            $preAttachments[$attachment->getId()] = (int)$attachment->getId();
        }

        $delete  = array_diff($preAttachments, $attachments);
        $insert  = array_diff($attachments, $preAttachments);
        $adapter = $this->getConnection();
        if (!empty($delete)) {
            $condition = [
                'product_id=?'        => (int)$productId,
                'attachment_id IN(?)' => array_keys($delete)
            ];
            $adapter->delete($this->getTable(self::PRODUCT_RELATION_TABLE), $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $attachmentId) {
                $data[] = [
                    'product_id'    => (int)$productId,
                    'attachment_id' => (int)$attachmentId,
                ];
            }
            $adapter->insertMultiple($this->getTable(self::PRODUCT_RELATION_TABLE), $data);
        }

        return $this;
    }

    /**
     * Retrieve attachment IDs
     *
     * @param int $sectionId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSectionAttachments($sectionId)
    {
        $select = $this->getConnection()->select()
                       ->from($this->getMainTable(), 'attachment_id')
                       ->where('section_id = ?', (int)$sectionId);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Retrieve customer group ids of specified attachment
     *
     * @param int $attachmentId
     * @return array
     */
    public function getCustomerGroupIds($attachmentId)
    {
        $select = $this->getConnection()->select()
                       ->from($this->getTable(self::CUSTOMER_GROUP_RELATION_TABLE), ['customer_group_id'])
                       ->where('attachment_id = ?', $attachmentId);

        return (array)$this->getConnection()->fetchCol($select);
    }

    /**
     *
     * @param int $sectionId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCountAttachments($sectionId)
    {
        $select = $this->getConnection()->select()
                       ->from($this->getMainTable(), new \Zend_Db_Expr('COUNT(' . $this->getIdFieldName() . ')'))
                       ->where('section_id = ?', (int)$sectionId);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     *
     * @param \MageWorx\Downloads\Model\Attachment $object
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment
     */
    public function clearProductRelation(\MageWorx\Downloads\Model\Attachment $object)
    {
        $id        = $object->getId();
        $condition = ['attachment_id=?' => $id];
        $this->getConnection()->delete($this->getTable(self::PRODUCT_RELATION_TABLE), $condition);
        $object->setIsChangedProductList(true);

        return $this;
    }


    /**
     * Process attachment data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['attachment_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable(self::STORE_RELATION_TABLE), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * @param $object
     * @throws \Exception
     */
    public function addTranslatedData($object)
    {
        $storeRelatedData = $this->lookupTranslatedStrings($object->getId());

        if ($storeRelatedData) {
            $namesArray        = [];
            $descriptionsArray = [];

            foreach ($storeRelatedData as $row) {
                if ($row['store_id'] == 0) {
                    $object->setName($row['name'])
                           ->setDescription($row['description']);
                } else {
                    $namesArray[$row['store_id']]        = $row['name'];
                    $descriptionsArray[$row['store_id']] = $row['description'];
                }
            }

            $object->setStoreAttachmentNames($namesArray)
                   ->setStoreAttachmentDescriptions($descriptionsArray);
        }
    }

    /**
     * @param int $attachmentId
     * @return array
     * @throws \Exception
     */
    public function lookupTranslatedStrings($attachmentId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
                             ->from($this->getTable('mageworx_downloads_attachment_locale'))
                             ->where($this->_idFieldName . ' = :attachment_id');

        return $connection->fetchAll($select, ['attachment_id' => (int)$attachmentId]);
    }

    /**
     * Get customer group ids to which specified item is assigned
     *
     * @param int $attachmentId
     * @return array
     */
    public function lookupCustomerGroupIds($attachmentId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable(self::CUSTOMER_GROUP_RELATION_TABLE),
            'customer_group_id'
        )->where(
            'attachment_id = ?',
            (int)$attachmentId
        );

        return $connection->fetchCol($select);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Cms\Model\Page $object
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$object->getStoreId()];
            $select->join(
                ['attachmentstore' => $this->getTable(self::STORE_RELATION_TABLE)],
                $this->getMainTable() . '.attachment_id = attachmentstore.attachment_id',
                []
            )->where(
                'is_active = ?',
                1
            )->where(
                'attachmentstore.store_id IN (?)',
                $storeIds
            )->order(
                'attachmentstore.store_id DESC'
            )->limit(
                1
            );
        }

        return $select;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $attachmentId
     * @return array
     */
    public function lookupStoreIds($attachmentId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable(self::STORE_RELATION_TABLE),
            'store_id'
        )->where(
            'attachment_id = ?',
            (int)$attachmentId
        );

        return $connection->fetchCol($select);
    }

    /**
     * Retrieve store model
     *
     * @return \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore()
    {
        return $this->storeManager->getStore($this->store);
    }

    /**
     * Get all existing attachment locale
     *
     * @param int $attachmentId
     * @return array
     */
    public function getExistsStoreLocaleData($attachmentId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(self::ATTACHMENT_LOCALE_RELATION_TABLE),
            ['store_id', 'name', 'description']
        )->where(
            'attachment_id = :attachment_id'
        );

        return $this->getConnection()->fetchAll($select, [':attachment_id' => $attachmentId]);
    }

    /**
     * Get attachment label by specific store id
     *
     * @param int $attachmentId
     * @param int $storeId
     * @return string
     */
    public function getStoreLocale($attachmentId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(self::ATTACHMENT_LOCALE_RELATION_TABLE),
            ['name', 'description']
        )->where(
            'attachment_id = :attachment_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );

        return $this->getConnection()->fetchOne(
            $select,
            [':attachment_id' => $attachmentId, ':store_id' => $storeId]
        );
    }


    /**
     * Assign attachment to store views, products, customer groups
     *
     * @param AbstractModel|\MageWorx\Downloads\Model\Attachment $object
     * @return $this
     * @throws \Exception
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->saveStoreRelation($object);
        $this->saveProductRelation($object);
        $this->saveCustomerGroupRelation($object);
        $this->saveLocaleRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \MageWorx\Downloads\Api\Data\AttachmentInterface $object
     * @return $this
     */
    protected function saveStoreRelation(\MageWorx\Downloads\Api\Data\AttachmentInterface $object)
    {
        if ($object->getStoreIds() === null) {
            return $this;
        }

        $newStores = (array)$object->getStoreIds();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }

        $oldStores = $this->lookupStoreIds($object->getId());

        $table  = $this->getTable(self::STORE_RELATION_TABLE);
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['attachment_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['attachment_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return $this;
    }

    /**
     * @param \MageWorx\Downloads\Model\Attachment $object
     * @return $this
     */
    public function saveProductRelation(\MageWorx\Downloads\Model\Attachment $object)
    {
        $products = $object->getProductIds();

        if ($products === null) {
            return $this;
        }
        $this->clearProductRelation($object);
        $id = $object->getId();

        if (!empty($products)) {
            $data = [];
            foreach ($products as $productId) {
                $data[] = [
                    'attachment_id' => (int)$id,
                    'product_id'    => (int)$productId,
                ];
            }
            $this->getConnection()->insertMultiple($this->getTable(self::PRODUCT_RELATION_TABLE), $data);
        }

        return $this;
    }

    /**
     * @param \MageWorx\Downloads\Model\Attachment $object
     * @return $this
     */
    public function saveCustomerGroupRelation(\MageWorx\Downloads\Model\Attachment $object)
    {
        $customerGroupIds = $object->getCustomerGroupIds();
        if ($customerGroupIds === null) {
            return $this;
        }
        $this->clearCustomerGroupRelation($object);
        $id = $object->getId();

        if (!empty($customerGroupIds)) {
            $data = [];
            foreach ($customerGroupIds as $customerGroupId) {
                $data[] = [
                    'attachment_id'     => (int)$id,
                    'customer_group_id' => (int)$customerGroupId,
                ];
            }
            $this->getConnection()->insertMultiple($this->getTable(self::CUSTOMER_GROUP_RELATION_TABLE), $data);
        }

        return $this;
    }

    /**
     *
     * @param \MageWorx\Downloads\Model\Attachment $object
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment
     */
    protected function clearCustomerGroupRelation(\MageWorx\Downloads\Model\Attachment $object)
    {
        $id        = $object->getId();
        $condition = ['attachment_id=?' => $id];
        $this->getConnection()->delete($this->getTable(self::CUSTOMER_GROUP_RELATION_TABLE), $condition);
        $object->setIsChangedCustomerGroupList(true);

        return $this;
    }

    /**
     * @param \MageWorx\Downloads\Api\Data\AttachmentInterface $object
     * @return $this
     */
    public function saveLocaleRelation(\MageWorx\Downloads\Api\Data\AttachmentInterface $object)
    {
        $attachmentId = $object->getId();
        $locales      = $object->getStoreLocales();

        if ($locales) {
            $deleteByStoreIds = [];
            $table            = $this->getTable(self::ATTACHMENT_LOCALE_RELATION_TABLE);
            $connection       = $this->getConnection();

            $data = [];
            foreach ($locales as $locale) {

                if ($this->string->strlen($locale->getStoreName())
                    || $this->string->strlen($locale->getStoreDescription())
                ) {
                    $data[] = [
                        'attachment_id' => $attachmentId,
                        'store_id'      => $locale->getStoreId(),
                        'name'          => $locale->getStoreName(),
                        'description'   => $locale->getStoreDescription()
                    ];
                } else {
                    $deleteByStoreIds[] = $locale->getStoreId();
                }
            }

            $connection->beginTransaction();
            try {

                if (!empty($data)) {
                    $connection->insertOnDuplicate($table, $data, ['name', 'description']);
                }

                if (!empty($deleteByStoreIds)) {
                    $connection->delete(
                        $table,
                        [
                            'attachment_id=?' => $attachmentId,
                            'store_id IN (?)' => $deleteByStoreIds
                        ]
                    );
                }
            } catch (\Exception $e) {
                $connection->rollBack();
                throw $e;
            }
            $connection->commit();
        }

        return $this;
    }
}
