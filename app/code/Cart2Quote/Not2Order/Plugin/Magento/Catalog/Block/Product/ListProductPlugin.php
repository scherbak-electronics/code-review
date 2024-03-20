<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Catalog\Block\Product;

/**
 * Class ListProductPlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Catalog\Block\Product
 */
class ListProductPlugin extends \Cart2Quote\Not2Order\Plugin\BasePlugin
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * ListProductPlugin constructor.
     *
     * @param \Cart2Quote\Not2Order\Html\Parser $parser
     * @param \Cart2Quote\Not2Order\Helper\Data $dataHelper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Cart2Quote\Not2Order\Html\Parser $parser,
        \Cart2Quote\Not2Order\Helper\Data $dataHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        parent::__construct($parser, $dataHelper);
        $this->request = $request;
    }

    /**
     * Remove add to cart from list/grid view.
     *
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(\Magento\Catalog\Block\Product\ListProduct $subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        //do not process empty result
        if (!$result) {
            return $result;
        }

        $this->parser->loadHtml($result);
        $containsString = $this->dataHelper->getContainsString();
        $xpath = sprintf(
            '//form[@data-role="tocart-form"]/input[@type="hidden"][@name="product"]/parent::form/button[%s]',
            $containsString
        );

        $domNodeList = $this->parser->xpath($xpath);
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $replace = $this->dataHelper->replaceButtonCheck();
        $replaceButton = $this->dataHelper->getReplaceButton();

        foreach ($domNodeList as $domNode) {
            $form = $domNode->parentNode;

            foreach ($form->childNodes as $childNode) {
                if ($childNode->nodeName == 'input' && $childNode->getAttribute('type') == 'hidden' &&
                    $childNode->getAttribute('name') == 'product') {
                    $value = $childNode->getAttribute('value');
                    $actualProduct = $subject->getLoadedProductCollection()->getItemById($value);

                    $show = $this->dataHelper->hideOrderButton($actualProduct, $customerGroupId);
                    if (!$show && !$replace) {
                        $domNode->parentNode->removeChild($domNode);
                    } elseif (!$show && $replace) {
                        $fragment = $this->parser->getDom()->createDocumentFragment();
                        $fragment->appendXML($replaceButton);
                        $domNode->parentNode->replaceChild($fragment, $domNode);
                    }
                }
            }
        }

        $result = $this->parser->getHtml();
        return $result;
    }

    /**
     * Function to remove products where show price is false from collection when filtered on price
     *
     * @param \Magento\Catalog\Model\Layer $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function afterGetLoadedProductCollection(\Magento\Catalog\Block\Product\ListProduct $subject, $collection)
    {
        if ($this->dataHelper->isModuleOutputDisabled() || !$this->request->getParam('price') || !$collection->isLoaded()) {
            return $collection;
        }

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection->getItems() as $product) {
            if (!$this->dataHelper->showPrice($product, $this->dataHelper->getCustomerGroupId())) {
                $collection->removeItemByKey($product->getEntityId());
            }
        }

        return $collection;
    }
}
