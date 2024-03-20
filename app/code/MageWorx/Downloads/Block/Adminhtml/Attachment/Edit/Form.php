<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Adminhtml\Attachment\Edit;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;

class Form extends GenericForm
{
    protected $_template = 'MageWorx_Downloads::widget/form.phtml';

    /**
     * Prepare form
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        if ($this->getRequest()->getParam('attachment_id') === null) {
            $this->setData('action', $this->getUrl('*/*/addAttachments'));
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'      => 'edit_form',
                    'action'  => $this->getData('action'),
                    'method'  => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $this->setChild(
            'form_after_switcher',
            $this->getLayout()->createBlock(\MageWorx\Downloads\Block\Adminhtml\Attachment\Edit\Switcher::class)
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
