<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\CatalogWidget\Block\Product;

use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Cart2Quote\Not2Order\Plugin\BasePlugin;
use Cart2Quote\Not2Order\Helper\Data;
use Cart2Quote\Not2Order\Html\Parser;

/**
 * Class ProductListPlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\CatalogWidget\Block\Product
 */
class ProductListPlugin extends BasePlugin
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * ProductListPlugin constructor.
     * @param Parser $parser
     * @param Data $dataHelper
     * @param ProductRepository $productRepository
     */
    public function __construct(Parser $parser, Data $dataHelper, ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        parent::__construct($parser, $dataHelper);
    }

    /**
     * Remove add to cart from homepage.
     *
     * @param ProductsList $result
     * @return ProductsList|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterToHtml(ProductsList $subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
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
                    $actualProduct = null;
                    $actualProduct = $this->productRepository->getById($value);
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
}
