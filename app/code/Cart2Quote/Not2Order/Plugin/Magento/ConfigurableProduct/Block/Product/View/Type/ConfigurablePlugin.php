<?php
/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type;

/**
 * Class ConfigurablePlugin
 *
 * @package Cart2Quote\Not2Order\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type
 */
class ConfigurablePlugin
{
    /**
     * @var \Cart2Quote\Not2Order\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * ConfigurablePlugin constructor.
     *
     * @param \Cart2Quote\Not2Order\Helper\Data $dataHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        \Cart2Quote\Not2Order\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;
        $this->json = $json;
    }

    /**
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param string $result
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return string
     */
    public function afterGetJsonConfig(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, $result)
    {
        $config = $this->json->unserialize($result);
        foreach ($subject->getAllowProducts() as $product) {
            $config['is_not2orderable'][$product->getId()] = $this->dataHelper->hideOrderButton(
                $product,
                $this->customerSession->getCustomerGroupId()
            );
            $config['not2order_hide_price'][$product->getId()] = $this->dataHelper->showPrice(
                $product,
                $this->customerSession->getCustomerGroupId()
            );
        }

        return $this->json->serialize($config);
    }
}
