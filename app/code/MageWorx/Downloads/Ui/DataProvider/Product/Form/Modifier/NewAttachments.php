<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Framework\Convert\DataObject as ObjectConverter;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\Store as StoreModel;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\Wysiwyg;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;
use MageWorx\Downloads\Model\Source\EnabledSections;
use MageWorx\Downloads\Model\Attachment as AttachmentModel;
use MageWorx\Downloads\Model\Attachment\Source\AssignType;
use MageWorx\Downloads\Model\Attachment\Source\ContentType as ContentTypeOptions;
use MageWorx\Downloads\Model\Attachment\Source\IsActive as IsActiveOptions;
use Magento\Downloadable\Model\Source\TypeUpload;
use MageWorx\Downloads\Helper\Data as HelperData;

/**
 * Class NewAttachments
 */
class NewAttachments extends AbstractModifier
{
    const DATA_SCOPE_NEW_ATTACHMENTS = 'new_attachments';
    const GROUP_DOWNLOADS            = 'mageworx_downloads';

    const SECTION_FIELD_INDEX         = 'mageworx_downloads_new_attachments_section_field';
    const NAME_FIELD_INDEX            = 'mageworx_downloads_new_attachments_name_field';
    const DESCRIPTION_FIELD_INDEX     = 'mageworx_downloads_new_attachments_description_field';
    const DOWNLOADS_LIMIT_FIELD_INDEX = 'mageworx_downloads_new_attachments_downloads_limit_field';
    const ASSIGN_TYPE_FIELD_INDEX     = 'mageworx_downloads_new_attachments_assign_type_field';
    const CONTENT_TYPE_FIELD_INDEX    = 'mageworx_downloads_new_attachments_content_type_field';
    const MULTI_FILE_FIELD_INDEX      = 'mageworx_downloads_new_attachments_multi_file_field';
    const URL_FIELD_INDEX             = 'mageworx_downloads_new_attachments_url_field';
    const CUSTOMER_GROUPS_FIELD_INDEX = 'mageworx_downloads_new_attachments_customer_groups_field';
    const STORES_FIELD_INDEX          = 'mageworx_downloads_new_attachments_stores_field';
    const IS_ACTIVE_FIELD_INDEX       = 'mageworx_downloads_new_attachments_is_active_field';

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var string
     */
    protected $scopeName;

    /**
     * @var string
     */
    protected $scopePrefix;

    /**
     * @var EnabledSections
     */
    protected $enabledSectionOptions;

    /**
     * @var ContentTypeOptions
     */
    protected $contentTypeOptions;

    /**
     * @var ObjectConverter
     */
    protected $objectConverter;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var IsActiveOptions
     */
    protected $isActiveOptions;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var TypeUpload
     */
    protected $typeUpload;

    /**
     * NewAttachments constructor.
     *
     * @param EnabledSections $enabledSectionOptions
     * @param ContentTypeOptions $contentTypeOptions
     * @param ObjectConverter $objectConverter
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param Store $store
     * @param IsActiveOptions $isActiveOptions
     * @param HelperData $helperData
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param TypeUpload $typeUpload
     * @param ArrayManager $arrayManager
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        EnabledSections $enabledSectionOptions,
        ContentTypeOptions $contentTypeOptions,
        ObjectConverter $objectConverter,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        Store $store,
        IsActiveOptions $isActiveOptions,
        HelperData $helperData,
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        TypeUpload $typeUpload,
        ArrayManager $arrayManager,
        $scopeName = 'product_form.product_form',
        $scopePrefix = ''
    ) {
        $this->enabledSectionOptions = $enabledSectionOptions;
        $this->contentTypeOptions    = $contentTypeOptions;
        $this->objectConverter       = $objectConverter;
        $this->groupRepository       = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager          = $storeManager;
        $this->store                 = $store;
        $this->isActiveOptions       = $isActiveOptions;
        $this->helperData            = $helperData;
        $this->locator               = $locator;
        $this->urlBuilder            = $urlBuilder;
        $this->typeUpload            = $typeUpload;
        $this->arrayManager          = $arrayManager;
        $this->scopeName             = $scopeName;
        $this->scopePrefix           = $scopePrefix;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if (!empty($meta[static::GROUP_DOWNLOADS]['children'])) {
            $meta[static::GROUP_DOWNLOADS]['children'][$this->scopePrefix . static::DATA_SCOPE_NEW_ATTACHMENTS] =
                $this->getNewAttachmentsFieldset();
        }

        return $meta;
    }

    /**
     * Prepares config for the New Attachments fieldset
     *
     * @return array
     */
    protected function getNewAttachmentsFieldset()
    {
        $content = __(
            'Here you can attach new files to this product.'
        );

        return [
            'children'  => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add New Attachments'),
                    $this->scopePrefix . static::DATA_SCOPE_NEW_ATTACHMENTS
                ),
                'modal'      => $this->getGenericModal(
                    __('Add New Attachments')
                ),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label'             => __('New Attachments'),
                        'componentType'     => Fieldset::NAME,
                        'sortOrder'         => 10,
                    ],
                ],
            ]
        ];
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_DOWNLOADS . '.' . $scope . '.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'label'         => false,
                        'content'       => $content,
                        'template'      => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children'  => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component'     => 'Magento_Ui/js/form/components/button',
                                'actions'       => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'openModal',
                                    ]
                                ],
                                'title'         => $buttonTitle,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepares config for modal slide-out panel
     *
     * @param Phrase $title
     * @return array
     */
    protected function getGenericModal(Phrase $title)
    {
        $containerIndex = static::GROUP_DOWNLOADS . '_' . static::DATA_SCOPE_NEW_ATTACHMENTS;

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component'     => 'MageWorx_Downloads/js/modal/modal-component',
                        'componentType' => Modal::NAME,
                        'indexies'      => [
                            'section_field'         => static::SECTION_FIELD_INDEX,
                            'multi_file_field'      => static::MULTI_FILE_FIELD_INDEX,
                            'url_field'             => static::URL_FIELD_INDEX,
                            'customer_groups_field' => static::CUSTOMER_GROUPS_FIELD_INDEX,
                            'stores_field'          => static::STORES_FIELD_INDEX,
                            'is_active_field'       => static::IS_ACTIVE_FIELD_INDEX,
                            'downloads_limit_field' => static::DOWNLOADS_LIMIT_FIELD_INDEX
                        ],
                        'options'       => [
                            'title'   => $title,
                            'buttons' => [
                                [
                                    'text'    => __('Done'),
                                    'class'   => 'action-primary',
                                    'actions' => [
                                        'validateRequiredFields',
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children'  => [
                $containerIndex => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender'    => false,
                                'componentType' => Container::NAME,
                                'dataScope'     => 'data',
                                'render_url'    => $this->urlBuilder->getUrl('mui/index/render')
                            ],
                        ],
                    ],
                    'children'  => [
                        $containerIndex . Fieldset::NAME                           => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label'         => __('Attachment Settings'),
                                        'componentType' => Fieldset::NAME,
                                        'collapsible'   => true,
                                        'sortOrder'     => 10,
                                        'opened'        => true,
                                    ],
                                ],
                            ],
                            'children'  => [
                                static::SECTION_FIELD_INDEX         => $this->getSectionConfig(10),
                                static::NAME_FIELD_INDEX            => $this->getNameConfig(
                                    StoreModel::DEFAULT_STORE_ID,
                                    20
                                ),
                                static::DESCRIPTION_FIELD_INDEX     => $this->getDescriptionConfig(
                                    StoreModel::DEFAULT_STORE_ID,
                                    30
                                ),
                                static::DOWNLOADS_LIMIT_FIELD_INDEX => $this->getDownloadsLimitConfig(40),
                                static::ASSIGN_TYPE_FIELD_INDEX     => $this->getAssignTypeConfig(50),
                                static::CONTENT_TYPE_FIELD_INDEX    => $this->getContentTypeConfig(60),
                                static::MULTI_FILE_FIELD_INDEX      => $this->getMultiFileNameConfig(70),
                                static::URL_FIELD_INDEX             => $this->getUrlConfig(80),
                                static::CUSTOMER_GROUPS_FIELD_INDEX => $this->getCustomerGroupsConfig(90),
                                static::STORES_FIELD_INDEX          => $this->getStoresConfig(100),
                                static::IS_ACTIVE_FIELD_INDEX       => $this->getIsActiveConfig(110),
                            ],
                        ],
                        $containerIndex . '_store_specific_data_' . Fieldset::NAME => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label'         => __('Store View Specific Data'),
                                        'componentType' => Fieldset::NAME,
                                        'collapsible'   => true,
                                        'sortOrder'     => 20,
                                        'opened'        => false,
                                    ],
                                ],
                            ],
                            'children'  => $this->getStoreSpecificDataChildren($containerIndex),
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getSectionConfig($sortOrder)
    {
        $sectionList = $this->enabledSectionOptions->toOptionArray();

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'required',
                        'label'             => __('Section'),
                        'componentType'     => Field::NAME,
                        'formElement'       => Select::NAME,
                        'options'           => $sectionList,
                        'dataScope'         => static::DATA_SCOPE_NEW_ATTACHMENTS . '.section_id',
                        'dataType'          => Number::NAME,
                        'sortOrder'         => $sortOrder,
                        'value'             => $sectionList[0]['value']
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $storeId
     * @param int $sortOrder
     * @return array
     */
    protected function getNameConfig(int $storeId, int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Name'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => 'store_attachment_names[' . $storeId . ']',
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $storeId
     * @param int $sortOrder
     * @return array
     */
    protected function getDescriptionConfig(int $storeId, int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'             => __('Description'),
                        'component'         => 'Magento_Ui/js/form/element/wysiwyg',
                        'template'          => 'ui/form/field',
                        'componentType'     => Field::NAME,
                        'formElement'       => Wysiwyg::NAME,
                        'dataScope'         => 'store_attachment_descriptions[' . $storeId . ']',
                        'dataType'          => Wysiwyg::NAME,
                        'sortOrder'         => $sortOrder,
                        'additionalClasses' => 'admin__control-wysiwig',
                        'validation'        => [
                            'required-entry' => false
                        ],
                        'listens'           => [
                            'disabled' => 'setDisabled',
                            'value'    => 'value'
                        ],
                        'wysiwyg'           => true,
                        'fit'               => true,
                        'wysiwygConfigData' => [
                            'add_variables'          => false,
                            'add_widgets'            => false,
                            'add_images'             => false,
                            'use_container'          => true,
                            'height'                 => '100px',
                            'is_pagebuilder_enabled' => false
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getDownloadsLimitConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Downloads Limit'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => static::DATA_SCOPE_NEW_ATTACHMENTS . '.downloads_limit',
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getAssignTypeConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Assign By'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'value'         => AssignType::ASSIGN_BY_IDS,
                        'dataScope'     => static::DATA_SCOPE_NEW_ATTACHMENTS . '.assign_type',
                        'dataType'      => Number::NAME,
                        'sortOrder'     => $sortOrder,
                        'visible'       => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getContentTypeConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('File / URL Switcher'),
                        'componentType' => Field::NAME,
                        'component'     => 'MageWorx_Downloads/js/form/element/content-type',
                        'formElement'   => Select::NAME,
                        'options'       => $this->contentTypeOptions->toOptionArray(),
                        'dataScope'     => static::DATA_SCOPE_NEW_ATTACHMENTS . '.type',
                        'dataType'      => Number::NAME,
                        'sortOrder'     => $sortOrder,
                        'indexies'      => [
                            'multi_file_field' => static::MULTI_FILE_FIELD_INDEX,
                            'url_field'        => static::URL_FIELD_INDEX
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getMultiFileNameConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'required',
                        'label'             => __('File(s)'),
                        'fileInputName'     => 'multifile',
                        'template'          => 'MageWorx_Downloads/form/element/uploader/uploader',
                        'previewTmpl'       => 'MageWorx_Downloads/form/element/uploader/preview',
                        'componentType'     => Field::NAME,
                        'formElement'       => 'fileUploader',
                        'dataScope'         => static::DATA_SCOPE_NEW_ATTACHMENTS . '.multifile',
                        'uploaderConfig'    => [
                            'url' => 'mageworx_downloads/attachment/uploader'
                        ],
                        'maxFileSize'       => $this->helperData->getMaximumAllowedFileSize(),
                        'isMultipleFiles'   => true,
                        'dataType'          => Text::NAME,
                        'sortOrder'         => $sortOrder,
                        'visible'           => true,
                        'visibleValue'      => ContentTypeOptions::CONTENT_FILE
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getUrlConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'required',
                        'label'             => __('URL'),
                        'componentType'     => Field::NAME,
                        'formElement'       => Input::NAME,
                        'dataScope'         => static::DATA_SCOPE_NEW_ATTACHMENTS . '.url',
                        'dataType'          => Text::NAME,
                        'sortOrder'         => $sortOrder,
                        'visible'           => false,
                        'visibleValue'      => ContentTypeOptions::CONTENT_URL
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getCustomerGroupsConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'required',
                        'label'             => __('Customer Groups'),
                        'componentType'     => Field::NAME,
                        'formElement'       => MultiSelect::NAME,
                        'dataScope'         => static::DATA_SCOPE_NEW_ATTACHMENTS . '.customer_group_ids',
                        'dataType'          => Text::NAME,
                        'sortOrder'         => $sortOrder,
                        'options'           => $this->getCustomerGroupIds(),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCustomerGroupIds()
    {
        $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return $this->objectConverter->toOptionArray($groups, 'id', 'code');
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getStoresConfig($sortOrder)
    {
        if ($this->storeManager->isSingleStoreMode()) {
            return [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'         => __('Store View'),
                            'componentType' => Field::NAME,
                            'formElement'   => MultiSelect::NAME,
                            'value'         => $this->storeManager->getDefaultStoreView()->getId(),
                            'dataScope'     => static::DATA_SCOPE_NEW_ATTACHMENTS . '.store_ids',
                            'dataType'      => Number::NAME,
                            'sortOrder'     => $sortOrder,
                            'visible'       => false,
                        ],
                    ],
                ],
            ];
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'required',
                        'label'             => __('Store View'),
                        'componentType'     => Field::NAME,
                        'formElement'       => MultiSelect::NAME,
                        'dataScope'         => static::DATA_SCOPE_NEW_ATTACHMENTS . '.store_ids',
                        'dataType'          => Text::NAME,
                        'sortOrder'         => $sortOrder,
                        'options'           => $this->store->getStoreValuesForForm(false, true),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getIsActiveConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'required',
                        'label'             => __('Is Active'),
                        'componentType'     => Field::NAME,
                        'formElement'       => Select::NAME,
                        'options'           => $this->isActiveOptions->toOptionArray(),
                        'dataScope'         => static::DATA_SCOPE_NEW_ATTACHMENTS . '.is_active',
                        'dataType'          => Number::NAME,
                        'sortOrder'         => $sortOrder,
                        'value'             => AttachmentModel::STATUS_DISABLED
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $containerIndex
     * @return array
     */
    protected function getStoreSpecificDataChildren(string $containerIndex): array
    {
        $children  = [];
        $sortOrder = 10;
        $stores    = $this->storeManager->getStores();

        uasort(
            $stores,
            function (StoreModel $store1, StoreModel $store2) {
                return $store1->getSortOrder() <=> $store2->getSortOrder();
            }
        );

        foreach ($stores as $store) {
            $storeId = (int)$store->getId();

            $children[$containerIndex . '_store_specific_data_fieldset_' . $storeId] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'         => $store->getWebsite()->getName() . ':  ' . $store->getName(),
                            'componentType' => Fieldset::NAME,
                            'collapsible'   => true,
                            'sortOrder'     => $sortOrder,
                        ],
                    ],
                ],
                'children'  => [
                    'store_attachment_name_' . $storeId        => $this->getNameConfig($storeId, 10),
                    'store_attachment_description_' . $storeId => $this->getDescriptionConfig($storeId, 20),

                ],
            ];

            $sortOrder += 10;
        }

        return $children;
    }
}
