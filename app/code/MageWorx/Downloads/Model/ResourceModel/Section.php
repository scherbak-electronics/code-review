<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\LocalizedException;

class Section extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const SECTION_LOCALE_RELATION_TABLE = 'mageworx_downloads_section_locale';

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * Section constructor.
     *
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->string = $string;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_downloads_section', 'section_id');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDuplicatedNames()
    {
        $select = $this->getConnection()
                       ->select()
                       ->from(['main_table' => $this->getMainTable()],
                              ''
                       )
                       ->join(
                           ['section_locale' => $this->getTable(self::SECTION_LOCALE_RELATION_TABLE)],
                           'main_table.section_id = section_locale.section_id AND section_locale.store_id = 0',
                           'name'

                       )
                       ->group('section_locale.name')
                       ->having('COUNT(`main_table`.`section_id`) > 1');

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param string $name
     * @param null $id
     * @return string
     */
    protected function findByName($name, $id = null)
    {
        $bind  = [':name' => $name, ':store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID];
        $where = 'name = :name AND store_id = :store_id';

        if ($id) {
            $bind['section_id'] = $id;
            $where              .= ' AND section_id != :section_id';
        }

        $select = $this->getConnection()
                       ->select()
                       ->from($this->getTable(self::SECTION_LOCALE_RELATION_TABLE), 'name')
                       ->where($where);

        return $this->getConnection()->fetchOne($select, $bind);
    }

    /**
     * @param AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->findByName($object->getName(), $object->getId())) {
            throw new LocalizedException(
                __('The same section name already exists. Please, rename the section and try again.')
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel|\MageWorx\Downloads\Model\Section $object
     * @return $this
     * @throws \Exception
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->saveLocaleRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \MageWorx\Downloads\Api\Data\SectionInterface $object
     * @return $this
     */
    public function saveLocaleRelation(\MageWorx\Downloads\Api\Data\SectionInterface $object)
    {
        $sectionId = $object->getId();
        $locales   = $object->getStoreLocales();

        if ($locales) {
            $deleteByStoreIds = [];
            $table            = $this->getTable(self::SECTION_LOCALE_RELATION_TABLE);
            $connection       = $this->getConnection();

            $data = [];
            foreach ($locales as $locale) {

                if ($this->string->strlen($locale->getStoreName())
                    || $this->string->strlen($locale->getStoreDescription())
                ) {
                    $data[] = [
                        'section_id'  => $sectionId,
                        'store_id'    => $locale->getStoreId(),
                        'name'        => $locale->getStoreName(),
                        'description' => $locale->getStoreDescription()
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
                            'section_id=?'    => $sectionId,
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

    /**
     * Check if section uses as default
     *
     * @param  \Magento\Framework\Model\AbstractModel $section
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $section)
    {
        if ($section->getId() == \MageWorx\Downloads\Api\Data\SectionInterface::DEFAULT_ID) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can\'t delete section ID#"%1".', $section->getId())
            );
        }
        return parent::_beforeDelete($section);
    }

    /**
     * Get all existing section's locales
     *
     * @param int $sectionId
     * @return array
     */
    public function getExistsStoreLocalesData($sectionId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(self::SECTION_LOCALE_RELATION_TABLE),
            ['store_id', 'name', 'description']
        )->where(
            'section_id = :section_id'
        );

        return $this->getConnection()->fetchAll($select, [':section_id' => $sectionId]);
    }
}
