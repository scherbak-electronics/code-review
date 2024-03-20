<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Helper;

use Magento\Store\Model\Store;

/**
 * Class Data
 * @package Cart2Quote\Desk\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_HELPDESK_ENABLED = 'desk_general/desk/enabled';
    const XML_PATH_DEFAULT_PRIORITY = 'desk_general/default_settings/priority';
    const XML_PATH_PRODUCT_PAGE_VISIBILITY = 'desk_general/default_settings/product_page_visibility';
    const XML_PATH_QUOTE_FRONT_PAGE_VISIBILITY = 'desk_general/default_settings/quote_front_page_visibility';
    const XML_PATH_QUOTE_BACK_PAGE_VISIBILITY = 'desk_general/default_settings/quote_back_page_visibility';
    const XML_PATH_CONTACT_FORM_PAGE_VISIBILITY = 'desk_general/default_settings/contact_page_visibility';
    const XML_PATH_CUSTOMER_CAN_EDIT = 'desk_general/default_settings/customer_can_edit_title';

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Store
     *
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * Priority Resource Model
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority
     */
    protected $priorityResourceModel;

    /**
     * Priority model factory
     *
     * @var \Cart2Quote\Desk\Model\Ticket\PriorityFactory
     */
    protected $priorityModelFactory;

    /**
     * Data constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority $priorityResourceModel
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filter,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority $priorityResourceModel,
        \Cart2Quote\Desk\Model\Ticket\PriorityFactory $priorityModelFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
        $this->filter = $filter;
        $this->priorityResourceModel = $priorityResourceModel;
        $this->priorityModelFactory = $priorityModelFactory;

        parent::__construct($context);
    }

    /**
     * Return short detail info in HTML
     *
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail)
    {
        return nl2br($this->filter->truncate($this->escapeHtml($origDetail), ['length' => 50]));
    }

    /**
     * Return label formatted for HTML
     *
     * @param string $label The label
     * @return string
     */
    public function getLabelHtml($label)
    {
        return $this->escapeHtml(ucfirst($label));
    }

    /**
     * Escape html for a string
     *
     * @param string $string
     * @return array|string
     */
    public function escapeHtml($string)
    {
        return $this->escaper->escapeHtml($string);
    }

    /**
     * Returns true if desk is enabled
     *
     * @return integer
     */
    public function getDeskEnabled()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_HELPDESK_ENABLED, $this->getStore()->getStoreId());
    }

    /**
     * Get the default priority from config
     *
     * @return integer
     */
    public function getDefaultPriority()
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_PRIORITY, $this->getStore()->getStoreId());
    }

    /**
     * Get the default priority code from
     *
     * @return string
     */
    public function getDefaultPriorityCode()
    {
        $priorityModel = $this->priorityModelFactory->create();
        $defaultPriorityId = $this->getDefaultPriority();

        $this->priorityResourceModel->load($priorityModel, $defaultPriorityId);
        return $priorityModel->getCode();
    }

    /**
     * Returns true if Product Page Visibility is enabled
     *
     * @return integer
     */
    public function getProductPageVisibility()
    {
        return $this->getConfigValue(self::XML_PATH_PRODUCT_PAGE_VISIBILITY, $this->getStore()->getStoreId());
    }

    /**
     * Returns true if Quote Frontend Page Visibility is enabled
     *
     * @return integer
     */
    public function getQuoteFrontPageVisibility()
    {
        return $this->getConfigValue(self::XML_PATH_QUOTE_FRONT_PAGE_VISIBILITY, $this->getStore()->getStoreId());
    }

    /**
     * Returns true if Quote Backend Page Visibility is enabled
     *
     * @return integer
     */
    public function getQuoteBackPageVisibility()
    {
        return $this->getConfigValue(self::XML_PATH_QUOTE_BACK_PAGE_VISIBILITY, $this->getStore()->getStoreId());
    }

    /**
     * Returns true if Contact Us Page Visibility is enabled
     *
     * @return integer
     */
    public function getContactFormVisibility()
    {
        return $this->getConfigValue(self::XML_PATH_CONTACT_FORM_PAGE_VISIBILITY, $this->getStore()->getStoreId());
    }

    /**
     * @return int
     */
    public function getCustomerCanEdit()
    {
        return $this->getConfigValue(self::XML_PATH_CUSTOMER_CAN_EDIT, $this->getStore()->getStoreId());
    }

    /**
     * Set current store
     *
     * @param Store $store
     * @return void
     */
    public function setStore(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        if ($this->store instanceof Store) {
            return $this->store;
        }

        return $this->storeManager->getStore();
    }

    /**
     * Return store configuration value
     *
     * @param string $path
     * @param int $storeId
     * @return integer
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
