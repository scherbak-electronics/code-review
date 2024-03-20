<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Customer\Message;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Customer\Model\Session;

/**
 * Class ListMessage
 * @package Cart2Quote\Desk\Controller\Customer\Message
 */
class ListMessage extends \Cart2Quote\Desk\Controller\Customer\Customer
{
    const LIST_MESSAGE_BLOCK = 'customer_ticket_view_message';
    const LIST_MESSAGE_HANDLE = 'desk_customer_message_listmessage';

    /**
     * JSON factory
     *
     * @var \Magento\Framework\Controller\Result\Json
     */
    protected $resultJson;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $messageRepositoryInterface;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Search criteria
     *
     * @var \Magento\Framework\Api\SearchCriteria
     */
    protected $searchCriteria;

    /**
     * Filter group
     *
     * @var \Magento\Framework\Api\Search\FilterGroupFactory
     */
    protected $filterGroupFactory;

    /**
     * API filter
     *
     * @var \Magento\Framework\Api\FilterFactory
     */
    protected $filterFactory;

    /**
     * Array of messages
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    protected $messages = [];

    /**
     * Class ListMessage constructor
     *
     * @param \Magento\Framework\Controller\Result\Json $resultJson
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory
     * @param \Magento\Framework\Api\FilterFactory $filterFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     * @param Session $customerSession
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\Json $resultJson,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory,
        \Magento\Framework\Api\FilterFactory $filterFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Cart2Quote\Desk\Helper\Data $dataHelper,
        Session $customerSession,
        Context $context
    ) {
        $this->resultJson = $resultJson;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroupFactory = $filterGroupFactory;
        $this->filterFactory = $filterFactory;
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $customerSession, $dataHelper);
    }

    /**
     * Get ticket messages
     * @return \Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $ticketId = $this->getRequest()->getParam('id');
        $currentMessagesCount = $this->getRequest()->getParam('last_id', 0);
        $messages = $this->getMessages($ticketId);
        $lastId = $this->getLastId($messages);

        if ($lastId == $currentMessagesCount) {
            $response = ['html' => ''];
        } else {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $layout = $resultPage->getLayout();

            $block = $layout->getBlock(self::LIST_MESSAGE_BLOCK);
            if ($block) {
                $returnHtml = $block->setMessage(reset($messages))->toHtml();
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        "The \"%s\" block is not set for the handle \"%s\".",
                        self::LIST_MESSAGE_BLOCK,
                        self::LIST_MESSAGE_HANDLE
                    )
                );

            }

            $response = ['html' => $returnHtml, 'lastId' => $lastId];
        }

        return $this->resultJson->setHttpResponseCode(200)->setData($response);
    }

    /**
     * Get a list of messages by Ticket Id
     *
     * @param int $ticketId
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    public function getMessages($ticketId)
    {
        if (!$this->messages && $ticketId) {
            $ticketFilter = $this->filterFactory->create()->setField('ticket_id')->setValue($ticketId);
            $privateFilter = $this->filterFactory->create()->setField('is_private')->setValue(0);

            $filterGroupTicketId = $this->filterGroupFactory->create()->setFilters([$ticketFilter]);
            $filterGroupIsPrivate = $this->filterGroupFactory->create()->setFilters([$privateFilter]);

            $this->searchCriteria->setFilterGroups([$filterGroupTicketId, $filterGroupIsPrivate]);
            $this->messages = $this->messageRepositoryInterface->getList($this->searchCriteria);
        }

        return $this->messages;
    }

    /**
     * Get the last message ID
     *
     * @param array $messages
     * @return bool|int
     */
    public function getLastId(array $messages)
    {
        /** @var \Cart2Quote\Desk\Api\Data\MessageInterface $firstMessage */
        $firstMessage = reset($messages);
        if ($firstMessage) {
            return $firstMessage->getId();
        } else {
            return false;
        }
    }
}
