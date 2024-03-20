<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Product\Tab;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Url;

/**
 * Class Form
 * @package Cart2Quote\Desk\Block\Product\Tab
 */
class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Catalog product model
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Rating model
     *
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $ratingFactory;

    /**
     * URL encoder
     *
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * Message manager interface
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * HTTP Context
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Customer URL
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * JS Layout
     *
     * @var array
     */
    protected $jsLayout;

    /**
     * Class form constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->urlEncoder = $urlEncoder;
        $this->productRepository = $productRepository;
        $this->ratingFactory = $ratingFactory;
        $this->messageManager = $messageManager;
        $this->httpContext = $httpContext;
        $this->customerUrl = $customerUrl;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
    }

    /**
     * Initialize ticket form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_template = 'Cart2Quote_Desk::product/view/tab/form.phtml';
    }

    /**
     * Get product info
     *
     * @return Product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductInfo()
    {
        return $this->productRepository->getById(
            $this->getProductId(),
            false,
            $this->_storeManager->getStore()->getId()
        );
    }

    /**
     * Get ticket product post action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getUrl(
            'desk/ticket/create',
            [
                '_secure' => $this->getRequest()->isSecure(),
                'id' => $this->getProductId(),
            ]
        );
    }

    /**
     * Get enquiry request post action
     *
     * @return string
     */
    public function getEnquiryAction()
    {
        return $this->getUrl(
            'desk/enquiry/create',
            [
                '_secure' => $this->getRequest()->isSecure(),
                'id' => $this->getProductId(),
            ]
        );
    }

    /**
     * Get current customer ID
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->currentCustomer->getCustomerId();
    }

    /**
     * Get product id
     *
     * @return int
     */
    protected function getProductId()
    {
        $productId = $this->getRequest()->getParam('product_id');
        if (!isset($productId)) {
            $productId = $this->getRequest()->getParam('id');
        }

        return $productId;
    }

    /**
     * Force disable cache
     * @return null
     */
    protected function getCacheLifetime()
    {
        return null;
    }
}
