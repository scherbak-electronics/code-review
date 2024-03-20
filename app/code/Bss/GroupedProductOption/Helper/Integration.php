<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GroupedProductOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GroupedProductOption\Helper;

class Integration extends \Magento\Framework\Url\Helper\Data
{
    /**
     * Allow stock alert
     */
    const XML_PATH_STOCK_ALLOW = 'bss_productstockalert/productstockalert/allow_stock';

    /**
     * Is enable stock alert
     *
     * @param int $store
     * @return bool
     */
    public function isStockAlertAllowed($store = null)
    {
        $stockOutPut = $this->_moduleManager->isOutputEnabled('Bss_ProductStockAlert');
        return $stockOutPut && $this->scopeConfig->isSetFlag(
            self::XML_PATH_STOCK_ALLOW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get post action
     *
     * @param int $productId
     * @return string
     */
    public function getPostAction($productId)
    {
        return $this->_getUrl(
            'productstockalert/add/stock',
            [
                'product_id' => $productId,
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
            ]
        );
    }

    /**
     * Get ajax url
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->_getUrl(
            'productstockalert/ajax/'
        );
    }
}
