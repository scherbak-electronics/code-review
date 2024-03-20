<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\Source;

use MageWorx\Downloads\Model\Source;

class EnabledSections extends Source
{
    /**
     * @var \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * EnabledSections constructor.
     *
     * @param \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory $collectionFactory
     */
    public function __construct(
        \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function toOptionArray()
    {
        if ($this->options === null) {

            $options = [];

            /** @var \MageWorx\Downloads\Model\ResourceModel\Section\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addEnabledFilter();
            $collection->addLocales(\Magento\Store\Model\Store::DEFAULT_STORE_ID);

            /** @var \MageWorx\Downloads\Model\Section $section */
            foreach ($collection as $section) {
                $options[] = [
                    'value' => $section->getSectionId(),
                    'label' => $section->getName(\Magento\Store\Model\Store::DEFAULT_STORE_ID)
                ];
            }

            $this->options = $options;
        }

        return $this->options;
    }
}
