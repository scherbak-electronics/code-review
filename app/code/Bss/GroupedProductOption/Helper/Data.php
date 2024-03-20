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

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PRODUCT_TYPE_CONFIGURABLE = 'configurable';
    const PRODUCT_TYPE_GROUPED = 'grouped';

    const XML_PATH_SHOW_SWATCH_TOOLTIP = 'catalog/frontend/show_swatch_tooltip';

    /**
     * Product drop-down option type.
     */
    const OPTION_TYPE_DROP_DOWN = 'drop_down';

    /**
     * Product multiple option type.
     */
    const OPTION_TYPE_MULTIPLE = 'multiple';

    /**
     * Product radio option type.
     */
    const OPTION_TYPE_RADIO = 'radio';

    /**
     * Product checkbox option type.
     */
    const OPTION_TYPE_CHECKBOX = 'checkbox';

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $resolver;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Filter\LocalizedToNormalized
     */
    protected $localizedToNormalized;

    /**
     * Data constructor.
     * @param Context $context
     * @param ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Locale\ResolverInterface $resolver
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Filter\LocalizedToNormalized $localizedToNormalized
     */
    public function __construct(
        Context $context,
        ProductMetadataInterface $productMetadata,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        \Magento\Framework\Escaper $escaper,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filter\LocalizedToNormalized $localizedToNormalized
    ) {
        parent::__construct($context);
        $this->productMetadata = $productMetadata;
        $this->registry = $registry;
        $this->resolver = $resolver;
        $this->escaper = $escaper;
        $this->cartHelper = $cartHelper;
        $this->logger = $logger;
        $this->localizedToNormalized = $localizedToNormalized;
    }

    /**
     * @return \Magento\Framework\Filter\LocalizedToNormalized
     */
    public function returnLocalizedToNormalized()
    {
        return $this->localizedToNormalized;
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function returnRegistry()
    {
        return $this->registry;
    }

    /**
     * @return \Magento\Framework\Locale\ResolverInterface
     */
    public function returnResolver()
    {
        return $this->resolver;
    }

    /**
     * @return \Magento\Framework\Escaper
     */
    public function returnEscaper()
    {
        return $this->escaper;
    }

    /**
     * @return \Magento\Checkout\Helper\Cart
     */
    public function returnCartHelper()
    {
        return $this->cartHelper;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function returnLogger()
    {
        return $this->logger;
    }

    /**
     * Get config values
     *
     * @param string $field
     * @return bool|string
     */
    public function getConfig($field = 'active')
    {
        if (!$this->scopeConfig->getValue(
            'groupedproductoption/general/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return false;
        }

        $result = $this->scopeConfig->getValue(
            'groupedproductoption/general/'.$field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     *  Compare magento version
     *
     * @param string $version
     * @return bool
     */
    public function isMagentoVersion($version)
    {
        $dataVersion = $this->productMetadata->getVersion();
        if (version_compare($dataVersion, $version) >= 0) {
            return true;
        }
        return false;
    }

    /**
     * Get config if swatch tooltips should be rendered.
     *
     * @return string
     */
    public function getShowSwatchTooltip()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHOW_SWATCH_TOOLTIP,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Compare magento edition
     *
     * @return bool
     */
    public function isCommunityEdition()
    {
        $edition = $this->productMetadata->getEdition();
        return $edition === 'Community';
    }
}
