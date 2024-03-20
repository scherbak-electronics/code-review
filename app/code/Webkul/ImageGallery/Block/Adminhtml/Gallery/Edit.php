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
namespace Webkul\ImageGallery\Block\Adminhtml\Gallery;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize imagegallery gallery edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'gallery_id';
        $this->_blockGroup = 'Webkul_ImageGallery';
        $this->_controller = 'adminhtml_gallery';
        parent::_construct();
        if ($this->_isAllowedAction('Webkul_ImageGallery::gallery')) {
            $this->buttonList->update('save', 'label', __('Save Gallery'));
        } else {
            $this->buttonList->remove('save');
        }
    }

    /**
     * Retrieve text for header element depending on loaded gallery
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('imagegallery_gallery')->getId()) {
            $title = $this->_coreRegistry->registry('imagegallery_gallery')->getTitle();
            $title = $this->escapeHtml($title);
            return __("Edit Gallery '%'", $title);
        } else {
            return __('New Gallery');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     *
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
