<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml ticket main block
 */
namespace Cart2Quote\Desk\Block\Adminhtml;

/**
 * Class GridContainer
 * @package Cart2Quote\Desk\Block\Adminhtml
 */
class GridContainer extends \Magento\Backend\Block\Widget\Grid\Container
{
    const BLOCK_GROUP = 'Cart2Quote_Desk';
    const CONTROLLER = 'adminhtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Customer Repository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Catalog product model factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Customer View Helper
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $customerViewHelper;

    /**
     * Class Main constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Helper\View $customerViewHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customerRepository = $customerRepository;
        $this->productFactory = $productFactory;
        $this->customerViewHelper = $customerViewHelper;
        parent::__construct($context, $data);
    }

    /**
     * Initialize add new ticket
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_addButtonLabel = __("New Ticket");
        parent::_construct();
        $this->_blockGroup = self::BLOCK_GROUP;
        $this->_controller = self::CONTROLLER;
        $this->setHeaderText();
        $this->addButton(
            'config',
            [
                'label' => __('Module Configuration'),
                'class' => 'action-secondary',
                'onclick' => sprintf(
                    'setLocation(\'%s\')',
                    $this->getUrl('adminhtml/system_config/edit/section/desk_general')),
            ],
            1
        );
    }

    /**
     * Get the header text
     *
     * @return void
     */
    protected function setHeaderText()
    {
        if ($customerName = $this->getCustomerName() != '') {
            $this->_headerText = __("All Tickets of Customer `%1`", $customerName);
        } elseif ($productName = $this->getProductName() != '') {
            $this->_headerText = __("All Ticket of Product `%1`", $productName);
        } else {
            $this->_headerText = __("All Tickets");
        }
    }

    /**
     * Get the product name
     *
     * @return string
     */
    protected function getProductName()
    {
        $productName = '';
        $productId = $this->getRequest()->getParam('productId', false);
        if ($productId) {
            $product = $this->productFactory->create()->load($productId);
            $productName = $this->escapeHtml($product->getName());
        }

        return $productName;
    }

    /**
     * Get Customer Name
     *
     * @return string
     */
    protected function getCustomerName()
    {
        $customerName = '';
        $customerId = $this->getRequest()->getParam('customerId', false);
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $customerName = $this->escapeHtml($this->customerViewHelper->getCustomerName($customer));
        }

        return $customerName;
    }
}
