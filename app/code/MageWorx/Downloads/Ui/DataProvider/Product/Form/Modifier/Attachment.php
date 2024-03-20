<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\Downloads\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\Store;
use MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory;
use MageWorx\Downloads\Model\ResourceModel\Attachment\Collection as AttachmentCollection;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use MageWorx\Downloads\Model\Attachment\Source\IsActive as IsActiveOptions;
use MageWorx\Downloads\Model\Source\EnabledSections as EnabledSectionOptions;
use MageWorx\Downloads\Model\Attachment\Link as AttachmentLink;

/**
 * Class Attachment
 */
class Attachment extends AbstractModifier
{
    const DATA_SCOPE            = '';
    const DATA_SCOPE_ATTACHMENT = 'attachment';
    const DATA_SCOPE_GROUP      = 'group';
    const GROUP_DOWNLOADS       = 'mageworx_downloads';

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    protected $scopeName;

    /**
     * @var string
     */
    protected $scopePrefix;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var IsActiveOptions
     */
    protected $isActiveOptions;

    /**
     * @var EnabledSectionOptions
     */
    protected $enabledSectionOptions;

    /**
     * @var AttachmentLink
     */
    protected $attachmentLink;

    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static $sortOrder = 90;

    /**
     * @var \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    protected $collection;

    /**
     * Attachment constructor.
     *
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param CollectionFactory $collectionFactory
     * @param IsActiveOptions $isActiveOptions
     * @param EnabledSectionOptions $enabledSectionOptions
     * @param AttachmentLink $attachmentLink
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory,
        IsActiveOptions $isActiveOptions,
        EnabledSectionOptions $enabledSectionOptions,
        AttachmentLink $attachmentLink,
        $scopeName = 'product_form.product_form',
        $scopePrefix = ''
    ) {
        $this->locator               = $locator;
        $this->urlBuilder            = $urlBuilder;
        $this->scopeName             = $scopeName;
        $this->scopePrefix           = $scopePrefix;
        $this->isActiveOptions       = $isActiveOptions;
        $this->collectionFactory     = $collectionFactory;
        $this->enabledSectionOptions = $enabledSectionOptions;
        $this->attachmentLink        = $attachmentLink;

        /** @var AttachmentCollection collection */
        $this->collection = $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_DOWNLOADS => [
                    'children'  => [
                        $this->scopePrefix . static::DATA_SCOPE_ATTACHMENT => $this->getAttachmentFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'         => __('Product Attachments'),
                                'collapsible'   => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope'     => static::DATA_SCOPE,
                                'sortOrder'     =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * @return array
     */
    protected function getDataScopes(): array
    {
        return [
            static::DATA_SCOPE_ATTACHMENT,
        ];
    }

    /**
     * Prepares config for the Attachments fieldset
     *
     * @return array
     */
    protected function getAttachmentFieldset(): array
    {
        $content = __(
            'Related attachments are shown to customers in "Attachments" tab on the product page.'
        );

        return [
            'children'  => [
                'button_set'                  => $this->getButtonSet(
                    $content,
                    __('Add Attachments'),
                    $this->scopePrefix . static::DATA_SCOPE_ATTACHMENT
                ),
                'modal'                       => $this->getGenericModal(
                    __('Add Attachments'),
                    $this->scopePrefix . static::DATA_SCOPE_ATTACHMENT
                ),
                static::DATA_SCOPE_ATTACHMENT => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_ATTACHMENT),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label'             => __('Attachments'),
                        'collapsible'       => false,
                        'componentType'     => Fieldset::NAME,
                        'dataScope'         => '',
                        'sortOrder'         => 10,
                    ],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product   = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }

        foreach ($this->getDataScopes() as $dataScope) {
            $data[$productId]['attachments'][$dataScope] = [];
            $this->collection->addProductFilter($productId)->addLocales(Store::DEFAULT_STORE_ID);

            foreach ($this->collection as $linkItem) {
                $data[$productId]['attachments'][$dataScope][] = $this->fillData($linkItem);
            }
        }

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;
        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_store_id']   = $this->locator->getStore()->getId();

        return $data;
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, string $scope): array
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_DOWNLOADS . '.' . $scope . '.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement'   => 'container',
                        'componentType' => 'container',
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
                                'formElement'   => 'container',
                                'componentType' => 'container',
                                'component'     => 'Magento_Ui/js/form/components/button',
                                'actions'       => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . self::GROUP_DOWNLOADS . '_product_' . $scope . '_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title'         => $buttonTitle,
                                'provider'      => null,
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
     * @param string $scope
     * @return array
     */
    protected function getGenericModal(Phrase $title, string $scope): array
    {
        $listingTarget = self::GROUP_DOWNLOADS . '_product_' . $scope . '_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope'     => '',
                        'options'       => [
                            'title'   => $title,
                            'buttons' => [
                                [
                                    'text'    => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text'    => __('Add Selected Attachments'),
                                    'class'   => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children'  => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender'         => false,
                                'componentType'      => 'insertListing',
                                'dataScope'          => $listingTarget,
                                'externalProvider'   => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.attachment_columns.ids',
                                'ns'                 => $listingTarget,
                                'render_url'         => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink'       => true,
                                'dataLinks'          => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType'      => 'simple',
                                'externalFilterMode' => true,
                                'imports'            => [
                                    'productId'     => '${ $.provider }:data.product.current_product_id',
                                    'storeId'       => '${ $.provider }:data.product.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false]

                                ],
                                'exports'            => [
                                    'productId'     => '${ $.externalProvider }:params.current_product_id',
                                    'storeId'       => '${ $.externalProvider }:params.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false]
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve grid
     *
     * @param string $scope
     * @return array
     */
    protected function getGrid(string $scope): array
    {
        $dataProvider = self::GROUP_DOWNLOADS . '_product_' . $scope . '_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses'        => 'admin__field-wide',
                        'componentType'            => DynamicRows::NAME,
                        'dndConfig'                => ['enabled' => false],
                        'label'                    => null,
                        'columnsHeader'            => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord'      => false,
                        'template'                 => 'ui/dynamic-rows/templates/grid',
                        'component'                => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton'                => false,
                        'recordTemplate'           => 'record',
                        'dataScope'                => 'data.attachments',
                        'deleteButtonLabel'        => __('Remove'),
                        'dataProvider'             => $dataProvider,
                        'map'                      => [
                            'id'            => 'attachment_id',
                            'section'       => 'section_text',
                            'name'          => 'name',
                            'filename'      => 'filename',
                            'type'          => 'type',
                            'url'           => 'url',
                            'date_modified' => 'date_modified',
                            'is_active'     => 'is_active_text',
                        ],
                        'links'                    => [
                            'insertData'    => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => ['insertData' => false]
                        ],
                        'sortOrder'                => 2,
                    ],
                ],
            ],
            'children'  => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate'    => true,
                                'is_collection' => true,
                                'component'     => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope'     => '',
                            ],
                        ],
                    ],
                    'children'  => $this->fillMeta(),
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    protected function fillMeta(): array
    {
        return [
            'id'            => $this->getTextColumn('id', false, __('ID'), 0),
            'section'       => $this->getTextColumn('section', false, __('Section'), 5),
            'name'          => $this->getTextColumn('name', false, __('Name'), 10),
            'filename'      => $this->getTextColumn('filename', false, __('Filename'), 20),
            'type'          => $this->getTextColumn('type', false, __('Type'), 30),
            'url'           => $this->getTextColumn('url', false, __('URL'), 40),
            'date_modified' => $this->getTextColumn('date_modified', false, __('Date Modified'), 50),
            'is_active'     => $this->getTextColumn('is_active', true, __('Is Active'), 60),
            'actionDelete'  => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses'     => 'data-grid-actions-cell',
                            'componentType'         => 'actionDelete',
                            'component'             => 'MageWorx_Downloads/js/dynamic-rows/cells/actions',
                            'template'              => 'MageWorx_Downloads/dynamic-rows/cells/actions',
                            'downloadLabel'         => __('Download'),
                            'downloadFileNameIndex' => 'filename',
                            'downloadBaseUrl'       => $this->attachmentLink->getBaseUrl(),
                            'dataType'              => Text::NAME,
                            'label'                 => __('Actions'),
                            'sortOrder'             => 70,
                            'fit'                   => true,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getTextColumn(string $dataScope, bool $fit, Phrase $label, int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'elementTmpl'   => 'ui/dynamic-rows/cells/text',
                        'component'     => 'Magento_Ui/js/form/element/text',
                        'dataType'      => Text::NAME,
                        'dataScope'     => $dataScope,
                        'fit'           => $fit,
                        'label'         => $label,
                        'sortOrder'     => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepare data column
     *
     * @param \MageWorx\Downloads\Model\Attachment $attachment
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function fillData(\MageWorx\Downloads\Model\Attachment $attachment): array
    {
        $isActiveOptions = $this->isActiveOptions->toArray();

        if (array_key_exists((int)$attachment->getIsActive(), $isActiveOptions)) {
            $status = $isActiveOptions[$attachment->getIsActive()];
        } else {
            $status = __('Unknown');
        }

        $sectionList = $this->enabledSectionOptions->toArray();
        if (array_key_exists($attachment->getSectionId(), $sectionList)) {
            $section = $sectionList[$attachment->getSectionId()];
        } else {
            $section = __('Unknown');
        }

        return [
            'id'            => $attachment->getId(),
            'section'       => $section,
            'name'          => $attachment->getName(),
            'filename'      => $attachment->getFilename(),
            'type'          => $attachment->getType(),
            'url'           => $attachment->getUrl(),
            'date_modified' => $attachment->getDateModified(),
            'is_active'     => $status,
        ];
    }
}
