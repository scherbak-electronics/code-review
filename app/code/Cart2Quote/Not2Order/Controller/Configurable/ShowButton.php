<?php
/**
 * Copyright (c) 2021. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Controller\Configurable;

use Magento\Framework\App\ActionInterface;

/**
 * Class ShowButton
 * @package Cart2Quote\Not2Order\Controller\Configurable
 */
class ShowButton implements ActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultFactory;

    /**
     * @var \Cart2Quote\Not2Order\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestInterface;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * ShowPrice constructor.
     *
     * @param \Magento\Framework\View\Result\PageFactory $resultFactory
     * @param \Cart2Quote\Not2Order\Helper\Data $dataHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultFactory,
        \Cart2Quote\Not2Order\Helper\Data $dataHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Catalog\Model\Product $product,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->resultFactory = $resultFactory;
        $this->dataHelper = $dataHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->requestInterface = $requestInterface;
        $this->product = $product;
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $productId = $this->requestInterface->getParam('id');
        $product = $this->product->load($productId);
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $productCartButtonConfig = $this->dataHelper->showButton($product, $customerGroupId);

        $result = $this->resultJsonFactory->create();
        $data = Array
        (
            'productSku' => $product->getSku(),
            'showButton' => $productCartButtonConfig
        );

        return $result->setData($data);
    }
}
