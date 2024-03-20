<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_ImageGallery
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\ImageGallery\Block\Adminhtml\Groups\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Webkul\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory;

class Gallery extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var CollectionFactory
     */
    protected $_galleryCollection;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param CollectionFactory $galleryCollection
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        CollectionFactory $galleryCollection,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_galleryCollection = $galleryCollection;
        $this->_jsonEncoder = $jsonEncoder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('imagegallery_groups_gallery');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_groups') {
            $selectedIds = $this->_getSelectedGallery();
            if (empty($selectedIds)) {
                $selectedIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', ['in' => $selectedIds]);
            } else {
                if ($selectedIds) {
                    $this->getCollection()->addFieldToFilter('id', ['nin' => $selectedIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return array|null
     */
    public function getGroup()
    {
        return $this->_coreRegistry->registry('imagegallery_groups');
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_galleryCollection->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_groups',
            [
                'type' => 'checkbox',
                'name' => 'in_groups',
                'values' => $this->_getSelectedGallery(),
                'index' => 'id'
            ]
        );
        $this->addColumn(
            'id',
            [
                'header' => __('Id'),
                'sortable' => true,
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'gallery_code',
            [
                'header' => __('Gallery Code'),
                'sortable' => true,
                'index' => 'gallery_code'
            ]
        );
        $this->addColumn(
            'image_ids',
            [
                'header' => __('Image Ids'),
                'sortable' => false,
                'filter' => false,
                'index' => 'image_ids'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('imagegallery/*/galleryGrid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedGallery()
    {
        $gallery = array_keys($this->getSelectedGallery());
        return $gallery;
    }

    /**
     * @return array
     */
    public function getSelectedGalleryJson()
    {
        $jsonGalleries = [];
        $galleries = array_keys($this->getSelectedGallery());
        foreach ($galleries as $gallery) {
            $jsonGalleries[$gallery] = 0;
        }
        return $this->_jsonEncoder->encode((object)$jsonGalleries);
    }

    /**
     * @return array
     */
    public function getSelectedGallery()
    {
        $gallery = [];
        $galleryIds = $this->getGroup()->getGalleryIds();
        $galleryIds = explode(",", $galleryIds);
        foreach ($galleryIds as $galleryId) {
            $gallery[$galleryId] = ['position' => $galleryId];
        }
        return $gallery;
    }

    /**
     * @return array
     */
    public function getGalleryIds()
    {
        $galleryIds = $this->getGroup()->getGalleryIds();
        return $galleryIds;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->getGroup()->getId();
    }
}
