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
namespace Webkul\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab;

use Webkul\ImageGallery\Model\ResourceModel\Images\CollectionFactory as ImagesCollection;

class Images extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var ImagesCollection
     */
    protected $_imagesCollection;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param ImagesCollection $imagesCollection,
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        ImagesCollection $imagesCollection,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_imagesCollection = $imagesCollection;
        $this->_jsonEncoder = $jsonEncoder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('imagegallery_gallery_images');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_gallery') {
            $selectedIds = $this->_getSelectedImages();
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
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_imagesCollection
                            ->create()
                            ->addFieldToFilter("status", "1");
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_gallery',
            [
                'type' => 'checkbox',
                'name' => 'in_gallery',
                'values' => $this->_getSelectedImages(),
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
            'id',
            [
                'header' => __('Thumbnail'),
                'sortable' => false,
                'filter' => false,
                'index' => 'id',
                'html_name' => 'thumb',
                'value' => $this->_getSelectedThumb(),
                'type' => 'radio'
            ]
        );
        $this->addColumn(
            'image',
            [
                'header' => __('Image'),
                'index' => 'image',
                'renderer'  => '\Webkul\ImageGallery\Block\Widget\Grid\Column\Renderer\Image',
                'filter'=> false
            ]
        );
        $this->addColumn('title', ['header' => __('Title'), 'index' => 'title']);
        $this->addColumn('description', ['header' => __('Description'), 'index' => 'description']);
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('imagegallery/*/imagesGrid', ['_current' => true]);
    }

    /**
     * @return array|null
     */
    public function getGallery()
    {
        return $this->_coreRegistry->registry('imagegallery_gallery');
    }

    /**
     * @return array|null
     */
    protected function _getSelectedImages()
    {
        $images = array_keys($this->getSelectedGalleryImages());
        return $images;
    }
    
    /**
     * @return int|null
     */
    protected function _getSelectedThumb()
    {
        return $this->getGallery()->getThumbnailShow();
    }
        
    public function getSelectedImagesJson()
    {
        $jsonUsers = [];
        $images = array_keys($this->getSelectedGalleryImages());
        foreach ($images as $key => $value) {
            $jsonUsers[$value] = 0;
        }
        return $this->_jsonEncoder->encode((object)$jsonUsers);
    }
    /**
     * @return array|null
     */
    public function getSelectedGalleryImages()
    {
        $images = [];
        $imageIds = $this->getGallery()->getImageIds();
        $imageIds = explode(",", $imageIds);
        foreach ($imageIds as $imageId) {
            $images[$imageId] = ['position' => $imageId];
        }
        return $images;
    }

    /**
     * @return array|null
     */
    public function getImageIds()
    {
        $imageIds = $this->getGallery()->getImageIds();
        return $imageIds;
    }
}
