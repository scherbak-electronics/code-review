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
use Magento\Store\Model\Store as StoreModel;
use Magento\Store\Model\System\Store;

class StoreSpecificData extends GenericForm implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $store;

    /**
     * @var WysiwygConfig
     */
    protected $wysiwygConfig;

    /**
     * StoreSpecificData constructor.
     *
     * @param Store $store
     * @param WysiwygConfig $wysiwygConfig
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Store $store,
        WysiwygConfig $wysiwygConfig,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->store         = $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return StoreSpecificData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\Downloads\Model\Section $section */
        $section     = $this->getSection();
        $sectionData = $this->_session->getData('mageworx_downloads_section_data', true);

        if ($sectionData) {
            $section->addData($sectionData);
        } else {
            if (!$section->getId()) {
                $section->addData($section->getDefaultValues());
            }
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('section_');
        $form->setFieldNameSuffix('section');

        $this->createStoreSpecificFieldsets($form);

        if ($section->getStoreLocales()) {
            $storeLocales = $this->convertDataForForm($section->getStoreLocales());
            $form->addValues($storeLocales);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param \MageWorx\Downloads\Api\Data\SectionLocaleInterface[] $storeLocales
     * @return array
     */
    protected function convertDataForForm($storeLocales): array
    {
        $data = [];

        foreach ($storeLocales as $storeLocale) {
            $data['store_section_name_' . $storeLocale->getStoreId()]        = $storeLocale->getStoreName();
            $data['store_section_description_' . $storeLocale->getStoreId()] = $storeLocale->getStoreDescription();
        }

        return $data;
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createStoreSpecificFieldsets($form)
    {
        $stores = $this->_storeManager->getStores();

        uasort(
            $stores,
            function (StoreModel $store1, StoreModel $store2) {
                return $store1->getSortOrder() <=> $store2->getSortOrder();
            }
        );

        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        foreach ($stores as $store) {

            $storeId  = $store->getId();
            $fieldset = $this->prepareFieldset($form, $store);

            $fieldset->addField(
                'store_section_name_' . $storeId,
                'text',
                [
                    'name'     => 'store_section_names[' . $storeId . ']',
                    'label'    => __('Name'),
                    'title'    => __('Name'),
                    'required' => false,
                ]
            );

            $wysiwygConfig = $this->wysiwygConfig->getConfig(
                ['hidden' => true, 'add_variables' => false, 'add_widgets' => false]
            );

            $fieldset->addField(
                'store_section_description_' . $storeId,
                'editor',
                [
                    'name'                => 'store_section_descriptions[' . $storeId . ']',
                    'label'               => __('Description'),
                    'title'               => __('Description'),
                    'config'              => $wysiwygConfig,
                    'required'            => false,
                    'fieldset_html_class' => 'store',
                ]
            );
        }

        return $fieldset;
    }

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param string $fieldName
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareTitle($store, $fieldName)
    {
        if ($store->getId() == StoreModel::DEFAULT_STORE_ID && $this->_storeManager->isSingleStoreMode()) {
            $title = $fieldName;
        } else {
            $title = $store->getWebsite()->getName() . ':  ' . $store->getName();
        }

        return $title;
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareFieldset(&$form, $store)
    {
        if ($this->_storeManager->isSingleStoreMode()) {
            return $form->addFieldset(
                'store_view_label_fieldset_' . $store->getId(),
                [
                    'class'  => 'store-scope',
                    'legend' => __('Name')
                ]
            );
        }

        $fieldset = $form->addFieldset(
            'store_view_label_fieldset_' . $store->getId(),
            [
                'legend' => $this->prepareTitle($store, __('Store View Specific Data')),
                'class'  => 'store-scope',
            ]
        );
        $renderer = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset::class
        );
        $fieldset->setRenderer($renderer);

        return $fieldset;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Store View Specific Data');
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
        return !$this->_storeManager->isSingleStoreMode();
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
     * @return \MageWorx\Downloads\Model\Section
     */
    protected function getSection()
    {
        return $this->_coreRegistry->registry('mageworx_downloads_section');
    }
}
