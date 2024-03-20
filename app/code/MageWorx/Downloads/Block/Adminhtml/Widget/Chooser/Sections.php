<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Adminhtml\Widget\Chooser;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use MageWorx\Downloads\Model\ResourceModel\Section\Grid\CollectionFactory;
use MageWorx\Downloads\Model\ResourceModel\Section\Grid\Collection;
use MageWorx\Downloads\Model\Section\Source\IsActive as IsActiveOptions;
use Magento\Backend\Block\Widget\Grid\Column;

class Sections extends \MageWorx\Downloads\Block\Adminhtml\Widget\Grid
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var IsActiveOptions
     */
    protected $isActiveOptions;

    /**
     * Sections constructor.
     *
     * @param Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param ElementFactory $elementFactory
     * @param CollectionFactory $collectionFactory
     * @param IsActiveOptions $isActiveOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        ElementFactory $elementFactory,
        CollectionFactory $collectionFactory,
        IsActiveOptions $isActiveOptions,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $elementFactory, $data);
        $this->collectionFactory = $collectionFactory;
        $this->isActiveOptions   = $isActiveOptions;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('section_id');
        $this->setDefaultDir(SortOrder::SORT_ASC);
        $this->setUseAjax(true);
        $this->setDefaultFilter(['section_ids' => 1]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'mageworx_downloads/section_widget/chooser',
            [
                '_current'       => true,
                'uniq_id'        => $this->getId(),
                'selected_items' => join(',', $this->getSelectedItems())
            ]
        );
    }

    /**
     * @return \MageWorx\Downloads\Block\Adminhtml\Widget\Grid
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Create grid columns
     *
     * @return \MageWorx\Downloads\Block\Adminhtml\Widget\Grid
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'section_ids',
            [
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select',
                'type'             => 'checkbox',
                'name'             => 'section_ids',
                'values'           => $this->getSelectedItems(),
                'index'            => 'section_id'
            ]
        );

        $this->addColumn(
            'section_id',
            [
                'header' => __('ID'),
                'align'  => 'left',
                'index'  => 'section_id',
                'width'  => 50
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Title'),
                'align'  => 'left',
                'index'  => 'name'
            ]
        );

        $this->addColumn(
            'description',
            [
                'header' => __('Description'),
                'align'  => 'left',
                'index'  => 'description'
            ]
        );


        $this->addColumn(
            'is_active',
            [
                'header'  => __('Enable'),
                'index'   => 'is_active',
                'type'    => 'options',
                'options' => $this->isActiveOptions->toArray()
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'section_ids') {
            /** @var Collection $collection */
            $collection = $this->getCollection();
            $sectionIds = $this->getSelectedItems();

            if (empty($sectionIds)) {
                $sectionIds = [0];
            }

            if ($column->getFilter()->getValue()) {
                $collection->addFieldToFilter('section_id', ['in' => $sectionIds]);
            } else {
                if ($sectionIds) {
                    $collection->addFieldToFilter('section_id', ['nin' => $sectionIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }
}
