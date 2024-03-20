<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use MageWorx\Downloads\Model\Source\EnabledSections;

class SectionText extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var EnabledSections
     */
    protected $enabledSectionOptions;

    /**
     * SectionText constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param EnabledSections $enabledSectionOptions
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        EnabledSections $enabledSectionOptions,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->enabledSectionOptions = $enabledSectionOptions;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName       = $this->getData('name');
        $sourceFieldName = 'section_id';

        $options = $this->enabledSectionOptions->toArray();

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName] = $options[$item[$sourceFieldName]];
        }

        return $dataSource;
    }
}
