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
namespace Webkul\ImageGallery\Block\Adminhtml\Images;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize Imagegallery Images Edit Block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'image_id';
        $this->_blockGroup = 'Webkul_ImageGallery';
        $this->_controller = 'adminhtml_images';
        parent::_construct();
        if ($this->_isAllowedAction('Webkul_ImageGallery::images')) {
            $this->buttonList->update('save', 'label', __('Save Image'));
        } else {
            $this->buttonList->remove('save');
        }
    }

    /**
     * Retrieve text for header element depending on loaded image
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('imagegallery_images')->getId()) {
            $title = $this->_coreRegistry->registry('imagegallery_images')->getTitle();
            $title = $this->escapeHtml($title);
            return __("Edit Image '%'", $title);
        } else {
            return __('New Image');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
