<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Blog\Model;

/**
 * Sitemap model
 * @package Aheadworks\Blog\Model
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * @var Sitemap\ItemsProvider
     */
    private $itemsProvider;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Sitemap\Helper\Data $sitemapData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory
     * @param \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory
     * @param \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $modelDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param Config $config
     * @param Sitemap\ItemsProvider $itemsProvider
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $escaper,
        \Magento\Sitemap\Helper\Data $sitemapData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory,
        \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory,
        \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $modelDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        Config $config,
        Sitemap\ItemsProvider $itemsProvider,
        array $data = []
    ) {

        parent::__construct(
            $context,
            $registry,
            $escaper,
            $sitemapData,
            $filesystem,
            $categoryFactory,
            $productFactory,
            $cmsFactory,
            $modelDate,
            $storeManager,
            $request,
            $dateTime,
            null,
            null,
            $data
        );
        $this->config = $config;
        $this->itemsProvider = $itemsProvider;
    }

    /**
     * Init sitemap items
     *
     * @return void
     */
    protected function _initSitemapItems()
    {
        parent::_initSitemapItems();
        $storeId = $this->getStoreId();
        if ($this->config->isBlogEnabled($storeId)) {
            $this->_sitemapItems[] = $this->itemsProvider->getBlogItem($storeId);
            $this->_sitemapItems[] = $this->itemsProvider->getCategoryItems($storeId);
            $this->_sitemapItems[] = $this->itemsProvider->getPostItems($storeId);
        }
    }
}
