<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Adminhtml\Widget\Chooser;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use MageWorx\Downloads\Model\ResourceModel\Attachment\Grid\CollectionFactory;
use MageWorx\Downloads\Model\ResourceModel\Attachment\Grid\Collection;
use MageWorx\Downloads\Model\Source\EnabledSections as SectionOptions;
use MageWorx\Downloads\Model\Attachment\Source\IsActive as IsActiveOptions;
use Magento\Backend\Block\Widget\Grid\Column;

class Attachments extends \MageWorx\Downloads\Block\Adminhtml\Widget\Grid
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SectionOptions
     */
    protected $sectionOptions;

    /**
     * @var IsActiveOptions
     */
    protected $isActiveOptions;

    /**
     * Attachments constructor.
     *
     * @param Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param ElementFactory $elementFactory
     * @param CollectionFactory $collectionFactory
     * @param SectionOptions $sectionOptions
     * @param IsActiveOptions $isActiveOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        ElementFactory $elementFactory,
        CollectionFactory $collectionFactory,
        SectionOptions $sectionOptions,
        IsActiveOptions $isActiveOptions,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $elementFactory, $data);
        $this->collectionFactory = $collectionFactory;
        $this->sectionOptions    = $sectionOptions;
        $this->isActiveOptions   = $isActiveOptions;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('attachment_id');
        $this->setDefaultDir(SortOrder::SORT_ASC);
        $this->setUseAjax(true);
        $this->setDefaultFilter(['attachment_ids' => 1]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'mageworx_downloads/attachment_widget/chooser',
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
            'attachment_ids',
            [
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select',
                'type'             => 'checkbox',
                'name'             => 'attachment_ids',
                'values'           => $this->getSelectedItems(),
                'index'            => 'attachment_id'
            ]
        );

        $this->addColumn(
            'attachment_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'index'  => 'attachment_id',
                'width'  => 50
            ]
        );

        $this->addColumn(
            'section_id',
            [
                'header'  => __('Section'),
                'align'   => 'left',
                'index'   => 'section_id',
                'type'    => 'options',
                'options' => $this->sectionOptions->toArray()
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'index'  => 'name'
            ]
        );

        $this->addColumn(
            'filename',
            [
                'header' => __('File Name'),
                'align'  => 'left',
                'index'  => 'filename'
            ]
        );

        $this->addColumn(
            'products_count',
            [
                'header'   => __('Products'),
                'align'    => 'left',
                'index'    => 'products_count',
                'filter'   => false,
                'sortable' => false
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
        if ($column->getId() == 'attachment_ids') {
            /** @var Collection $collection */
            $collection    = $this->getCollection();
            $attachmentIds = $this->getSelectedItems();

            if (empty($attachmentIds)) {
                $attachmentIds = [0];
            }

            if ($column->getFilter()->getValue()) {
                $collection->addFieldToFilter('attachment_id', ['in' => $attachmentIds]);
            } else {
                if ($attachmentIds) {
                    $collection->addExcludeAttachmentFilter($attachmentIds);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }
}
