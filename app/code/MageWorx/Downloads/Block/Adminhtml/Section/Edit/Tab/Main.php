<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Adminhtml\Section\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use MageWorx\Downloads\Model\Section\Source\IsActive as IsActiveOptions;

class Main extends GenericForm implements TabInterface
{
    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var IsActiveOptions
     */
    protected $isActiveOptions;

    /**
     * @var WysiwygConfig
     */
    protected $wysiwygConfig;

    /**
     * Main constructor.
     *
     * @param WysiwygConfig $wysiwygConfig
     * @param IsActiveOptions $isActiveOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        WysiwygConfig $wysiwygConfig,
        IsActiveOptions $isActiveOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->isActiveOptions = $isActiveOptions;
        $this->registry        = $registry;
        $this->wysiwygConfig   = $wysiwygConfig;
    }

    /**
     * Prepare form
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\Downloads\Model\Section $section */
        $section = $this->getSection();
        $form    = $this->_formFactory->create();
        $form->setHtmlIdPrefix('section_');
        $form->setFieldNameSuffix('section');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => $this->getLegendText(),
                'class'  => 'fieldset-wide'
            ]
        );

        if ($section->getId()) {
            $fieldset->addField(
                'section_id',
                'hidden',
                ['name' => 'section_id']
            );
        }

        $fieldset->addField(
            'store_section_name_0',
            'text',
            [
                'name'     => 'store_section_names[0]',
                'label'    => __('Name'),
                'title'    => __('Name'),
                'required' => true,
            ]
        );

        $wysiwygConfig = $this->wysiwygConfig->getConfig(
            ['hidden' => true, 'add_variables' => false, 'add_widgets' => false]
        );
        $wysiwygConfig->addData(['add_images' => false]);

        $fieldset->addField(
            'store_section_description_0',
            'editor',
            [
                'name'     => 'store_section_descriptions[0]',
                'label'    => __('Description'),
                'title'    => __('Description'),
                'config'   => $wysiwygConfig,
                'required' => false
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name'     => 'is_active',
                'label'    => __('Is Active'),
                'title'    => __('Is Active'),
                'required' => true,
                'options'  => $this->isActiveOptions->toArray()
            ]
        );

        $sectionData = $this->_session->getData('mageworx_downloads_section_data', true);

        if ($sectionData) {
            $section->addData($sectionData);
        } else {
            if (!$section->getId()) {
                $section->addData($section->getDefaultValues());
            }
        }

        if ($section->getStoreLocales()) {
            $storeLocales = $this->convertData($section->getStoreLocales());
            $form->addValues($storeLocales);
        }

        $form->addValues($section->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param \MageWorx\Downloads\Api\Data\SectionLocaleInterface[] $storeLocales
     * @return array
     */
    protected function convertData($storeLocales)
    {
        $data = [];

        foreach ($storeLocales as $storeLocale) {
            $data['store_section_name_' . $storeLocale->getStoreId()]        = $storeLocale->getStoreName();
            $data['store_section_description_' . $storeLocale->getStoreId()] = $storeLocale->getStoreDescription();
        }

        return $data;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Section Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     *
     * @return \MageWorx\Downloads\Model\Section
     */
    protected function getSection()
    {
        return $this->registry->registry('mageworx_downloads_section');
    }
}
