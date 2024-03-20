<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Bundle\Helper\Catalog\Product;

use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Helper\Product\Configuration as ProductConfiguration;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Bundle\Helper\Catalog\Product\Configuration;
use Magento\Framework\Escaper;
use Cart2Quote\Not2Order\Plugin\BasePlugin;
use Cart2Quote\Not2Order\Helper\Data;

/**
 * Class AbstractBlockPlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Framework\View\Element
 */
class ConfigurationPlugin extends Configuration
{
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * ConfigurationPlugin constructor.
     * @param Data $dataHelper
     */
    public function __construct(
        Context $context,
        ProductConfiguration $productConfiguration,
        PricingHelper $pricingHelper,
        Escaper $escaper,
        Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $productConfiguration, $pricingHelper, $escaper);
    }

    /**
     * Remove bundle product child price from shopping/quote cart
     *
     * @param Configuration $subject
     * @param $result
     * @return string
     */
    public function aroundGetBundleOptions(Configuration $subject, $result, $item)
    {
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $options = [];
        $product = $item->getProduct();

        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $product->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? json_decode($optionsQuoteItemOption->getValue()) : [];
        if ($bundleOptionsIds) {
            /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = json_decode($selectionsQuoteItemOption->getValue());

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds($bundleSelectionIds, $product);

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = ['label' => $bundleOption->getTitle(), 'value' => []];

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                if ($this->dataHelper->showPrice($bundleSelection, $customerGroupId)) {
                                    $option['value'][] = $qty . ' x '
                                        . $this->escaper->escapeHtml($bundleSelection->getName())
                                        . ' '
                                        . $this->pricingHelper->currency(
                                            $this->getSelectionFinalPrice($item, $bundleSelection)
                                        );
                                } else {
                                    $option['value'][] = $qty . ' x '
                                        . $this->escaper->escapeHtml($bundleSelection->getName());
                                }
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }
}
