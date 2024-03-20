<?php

namespace Customerprice\Attachment\Block\Adminhtml\Grid\Edit;

use Magento\Framework\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Customerprice\Attachment\Helper\GroupCode;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;
    protected $_uploaderFactory;
    protected $_storeManager;
    protected $groupCode;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Customerprice\Attachment\Model\Status $options,
        \Customerprice\Attachment\Model\Pricelist $optionsprice,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        GroupCode $groupCode,
        array $data = []
    ) 
    {
        $this->_options = $options;
        $this->_optionsprice = $optionsprice;
        $this->groupCode = $groupCode;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');

        // echo $this->getData('file'); die('test');

        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form', 
                            'enctype' => 'multipart/form-data', 
                            'action' => $this->getData('action'), 
                            'method' => 'post'
                        ]
            ]
        );

        $form->setHtmlIdPrefix('admingrid_');
        if ($model->getEntityId()) {

            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Row Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Row Data'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'id' => 'name',
                'title' => __('Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $customerType = $this->groupCode->getCustomerGroups();
        $fieldset->addField(
            'customer_type',
            'select',
            [
                'name' => 'customer_type',
                'label' => __('Customer Type'),
                'id' => 'customer_type',
                'title' => __('Customer Type'),
                'values' => $customerType,
                'class' => 'customergrid',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'pricelist_type',
            'select',
            [
                'name' => 'pricelist_type',
                'label' => __('Pricelist Type'),
                'id' => 'pricelist_type',
                'title' => __('Pricelist Type'),
                'values' => $this->_optionsprice->getOptionArrayPrice(),
                'class' => 'customergrid',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'id' => 'status',
                'title' => __('Status'),
                'values' => $this->_options->getOptionArray(),
                'class' => 'status',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'id' => 'position',
                'title' => __('Position'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $customer_file = $fieldset->addField(
            'file',
            'file',
            [
                'name' => 'file',
                'label' => __('File'),
                'title' => __('File'),
                'required' => true,
                'value' => $this->getData('file')
            ]
        );
        $customer_file->setAfterElementHtml("
        <span class='PrevfileName' style='display: grid; margin-top: 6px; font-size: 14px;'>Previous File: ".$model->getFile()."</span>
        ");

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}



