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

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Generic;

class Gallery extends Generic implements TabInterface
{
    /**
    * Prepare form
    *
    * @return $this
    */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('imagegallery_gallery');
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Gallery Information'), 'class' => 'fieldset-wide']
        );
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField('image_ids', 'hidden', ['name' => 'image_ids', 'id' => 'image_ids']);
        $fieldset->addField('image_ids_old', 'hidden', ['name' => 'image_ids_old']);

        $fieldset->addField(
            'gallery_code',
            'text',
            [
                'name' => 'gallery_code',
                'label' => __('Gallery Code'),
                'title' => __('Gallery Code'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'gallery_title',
            'text',
            [
                'name' => 'gallery_title',
                'label' => __('Gallery Title'),
                'title' => __('Gallery Title'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
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

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Gallery Data');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Gallery Data');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
