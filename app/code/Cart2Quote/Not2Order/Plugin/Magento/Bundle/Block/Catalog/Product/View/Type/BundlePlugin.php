<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Bundle\Block\Catalog\Product\View\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class BundlePlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Framework\View\Element
 */
class BundlePlugin
{
    /**
     * @var \Cart2Quote\Not2Order\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * BundlePlugin constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param \Cart2Quote\Not2Order\Helper\Data $dataHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Cart2Quote\Not2Order\Helper\Data $dataHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    ) {
        $this->dataHelper = $dataHelper;
        $this->jsonEncoder = $jsonEncoder;
        $this->productRepository = $productRepository;
    }

    /**
     * Remove price from bundle dropdown/multiselect product view.
     *
     * @param \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetJsonConfig(\Magento\Bundle\Block\Catalog\Product\View\Type\Bundle $subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        $decodedResult = json_decode($result, true);
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        if (isset($decodedResult['bundleId'])) {
            $bundleId = $decodedResult['bundleId'];
        }
        try {
            $product = $this->productRepository->getById($bundleId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $result;
        }
        $selectionCollection = $product
            ->getTypeInstance(true)
            ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product), $product
            );

        foreach ($decodedResult['options'] as $optionId => &$option) {
            foreach ($option['selections'] as $selectionId => &$selection) {
                if (!$this->dataHelper->showPrice($selectionCollection->getItemByid($selectionId), $customerGroupId)) {
                    if (isset($selection['prices']['finalPrice']['amount'])) {
                        $selection['prices']['finalPrice']['amount'] = '';
                    }
                }
                break;
            }
        }

        return json_encode($decodedResult);
    }
}
