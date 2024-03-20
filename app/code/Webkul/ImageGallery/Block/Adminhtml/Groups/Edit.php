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
namespace Webkul\ImageGallery\Block\Adminhtml\Groups;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize imagegallery groups edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'groups_id';
        $this->_blockGroup = 'Webkul_ImageGallery';
        $this->_controller = 'adminhtml_groups';
        parent::_construct();
        if ($this->_isAllowedAction('Webkul_ImageGallery::groups')) {
            $this->buttonList->update('save', 'label', __('Save Group'));
        } else {
            $this->buttonList->remove('save');
        }
    }

    /**
     * Retrieve text for header element depending on loaded Group
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('imagegallery_groups')->getId()) {
            $title = $this->_coreRegistry->registry('imagegallery_groups')->getTitle();
            $title = $this->escapeHtml($title);
            return __("Edit Group '%'", $title);
        } else {
            return __('New Group');
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
