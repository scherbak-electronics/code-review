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
namespace Webkul\ImageGallery\Block\Adminhtml\Gallery\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gallery_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gallery Information'));
    }

    /**
    * Prepare Layout
    *
    * @return $this
    */
    protected function _prepareLayout()
    {
        $block = 'Webkul\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab\Gallery';
        $imagesBlock = 'Webkul\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab\Images';
        $this->addTab(
            'gallery',
            [
                'label' => __('Gallery'),
                'content' => $this->getLayout()->createBlock($block, 'gallery')->toHtml(),
            ]
        );
        $this->addTab(
            'images',
            [
                'label' => __('Gallery Images'),
                'content' => $this->getLayout()
                            ->createBlock($imagesBlock, 'imagegallery.gallery.images.grid')->toHtml()
            ]
        );
        return parent::_prepareLayout();
    }
}
