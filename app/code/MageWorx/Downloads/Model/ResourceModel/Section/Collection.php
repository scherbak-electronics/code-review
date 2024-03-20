<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\ResourceModel\Section;

use MageWorx\Downloads\Model\Section;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $_idFieldName = 'section_id';

    /**
     * @var bool
     */
    protected $joinLocaleFlag = false;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }


    public function _construct()
    {
        $this->_init(
            \MageWorx\Downloads\Model\Section::class,
            \MageWorx\Downloads\Model\ResourceModel\Section::class
        );
        $this->_map['fields']['section_id'] = 'main_table.section_id';
    }

    /**
     * Add enabled filter
     *
     * @return \MageWorx\Downloads\Model\ResourceModel\Section\Collection
     */
    public function addEnabledFilter()
    {
        $this->getSelect()->where('main_table.is_active = ?', Section::STATUS_ENABLED);

        return $this;
    }

    /**
     *
     * @return \MageWorx\Downloads\Model\ResourceModel\Section\Collection
     */
    public function addSortOrder()
    {
        $this->getSelect()->order($this->getIdFieldName());

        return $this;
    }

    /**
     * @param null|int $storeId
     * @return \MageWorx\Downloads\Model\ResourceModel\Section\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addLocales($storeId = null)
    {
        if ($this->joinLocaleFlag) {
            return $this;
        }

        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        if ($storeId != 0) {
            $this->getSelect()
                 ->joinLeft(
                     ['section_locale_default' => $this->getTable('mageworx_downloads_section_locale')],
                     'main_table.section_id = section_locale_default.section_id AND section_locale_default.store_id = 0',
                     ''
                 )
                 ->joinLeft(
                     ['section_locale' => $this->getTable('mageworx_downloads_section_locale')],
                     'main_table.section_id = section_locale.section_id AND section_locale.store_id = ' . $storeId,
                     [
                         'name'        => "IF(section_locale.name!='', section_locale.name, section_locale_default.name)",
                         'description' => "IF(section_locale.description!='', section_locale.description, section_locale_default.description)"
                     ]
                 );
        } else {
            $this->getSelect()->joinLeft(
                ['section_locale_default' => $this->getTable('mageworx_downloads_section_locale')],
                'main_table.section_id = section_locale_default.section_id AND section_locale_default.store_id = 0',
                ['name', 'description']
            );
        }

        $this->joinLocaleFlag = true;

        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return \Magento\Framework\DataObject|mixed|null
     */
    public function getItemByCaseInsensitiveColumnValue($column, $value)
    {
        $this->load();

        foreach ($this as $item) {
            if (strnatcasecmp($item->getData($column), $value) === 0) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return \Magento\Framework\DataObject|mixed|null
     */
    public function getItemByСaseInsensitiveColumnValue($column, $value)
    {
        /** @var \MageWorx\Downloads\Model\Section $item */
        foreach ($this as $item) {
            if (strnatcasecmp($item->getData($column), $value) === 0) {
                return $item;
            }
        }
        return null;
    }
}
