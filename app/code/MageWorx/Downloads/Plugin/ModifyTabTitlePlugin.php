<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Plugin;

class ModifyTabTitlePlugin
{
    /**
     * @var \MageWorx\Downloads\Helper\Data
     */
    private $helperData;

    /**
     * ModifyTabTitlePlugin constructor.
     *
     * @param \MageWorx\Downloads\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\Downloads\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\Details $subject
     * @param $result
     * @param $alias
     * @param $key
     * @return mixed|string
     */
    public function afterGetChildData(
        \Magento\Catalog\Block\Product\View\Details $subject,
        $result,
        $alias,
        $key
    ) {
        if ($alias === 'mageworx_product_attachments' && $key === 'title') {
            $result = $this->helperData->getProductDownloadsTabTitle();
        }

        return $result;
    }
}
