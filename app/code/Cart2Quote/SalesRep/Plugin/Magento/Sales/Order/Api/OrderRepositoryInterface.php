<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Plugin\Magento\Sales\Order\Api;

use Cart2Quote\Quotation\Model\Quote;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Model\Order;

/**
 * Class OrderRepositoryInterface
 *
 * @package Cart2Quote\SalesRep\Plugin\Magento\Sales\Order\Api
 */
class OrderRepositoryInterface
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory $mageQuoteFactory
     */
    protected $mageQuoteFactory;

    /**
     * SalesRep repository
     *
     * @var \Cart2Quote\SalesRep\Api\UserRepositoryInterface
     */
    protected $salesRepRepository;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * OrderRepositoryInterface constructor
     *
     * @param \Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
     * @param \Cart2Quote\SalesRep\Api\UserRepositoryInterface $salesRepRepository
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param OrderExtensionFactory|null $orderExtensionFactory
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
        \Cart2Quote\SalesRep\Api\UserRepositoryInterface $salesRepRepository,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory = null
    ) {
        $this->quoteFactory = null;
        if (class_exists(\Cart2Quote\Quotation\Model\QuoteFactory::class)) {
            $this->quoteFactory = ObjectManager::getInstance()->get(\Cart2Quote\Quotation\Model\QuoteFactory::class);
        }

        $this->mageQuoteFactory = $mageQuoteFactory;
        $this->salesRepRepository = $salesRepRepository;
        $this->userFactory = $userFactory;

        $this->orderExtensionFactory = $orderExtensionFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Sales\Api\Data\OrderExtensionFactory::class);
    }

    /**
     * After get plugin
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order
     */
    public function afterGet($orderRepositoryInterface, $order)
    {
        return $this->addQuoteExtensionAttributesToOrder($order);
    }

    /**
     * After get list plugin
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Sales\Api\Data\OrderSearchResult $searchResult
     * @return \Magento\Sales\Api\Data\OrderSearchResult
     */
    public function afterGetList($orderRepositoryInterface, $searchResult)
    {
        foreach ($searchResult->getItems() as $order) {
            $this->addQuoteExtensionAttributesToOrder($order);
        }

        return $searchResult;
    }

    /**
     * Add the quote extension attributes to the order
     *
     * @param Order $order
     * @return Order
     */
    public function addQuoteExtensionAttributesToOrder($order)
    {
        //quoteFactory is null when C2Q is not available
        if ($this->quoteFactory === null) {
            return $order;
        }

        $quoteId = $order->getQuoteId();

        /** @var \Magento\Quote\Model\Quote $mageQuote */
        $mageQuote = $this->mageQuoteFactory->create()->load($quoteId);
        if (!$mageQuote->getId() || !$mageQuote->getLinkedQuotationId()) {
            //no mage quote data available
            return $order;
        }

        $quotationId = $mageQuote->getLinkedQuotationId();

        /** @var Quote $quotation */
        $quotation = $this->quoteFactory->create()->load($quotationId);
        if (!$quotation->getId()) {
            //no quote data available
            return $order;
        }

        $salesRep = $this->salesRepRepository->getMainUserByAssociatedId(
            $quoteId,
            \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_QUOTATION
        );
        if (!$salesRep->getId()) {
            //no sales rep data available
            return $order;
        }

        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        //modify extension attributes
        $user = $this->userFactory->create()->load($salesRep->getUserId());
        if ($user->getId()) {
            $extensionAttributes->setQuotationSalesrepEmail($user->getEmail());
        }

        //set extension attributes
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }
}
