<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Adminhtml\Ticket;

use Magento\Framework\Controller\ResultFactory;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class ListMessage
 * @package Cart2Quote\Desk\Controller\Adminhtml\Ticket
 */
class ListMessage extends \Magento\Backend\App\Action
{
    const LIST_MESSAGE_BLOCK = 'ticket.edit.container.messages.message';
    const LIST_MESSAGE_HANDLE = 'desk_ticket_listmessage';

    /**
     * JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

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
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    protected $filterGroup;

    /**
     * API filter
     *
     * @var \Magento\Framework\Api\Filter
     */
    protected $filter;

    /**
     * Array of messages
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    protected $messages;

    /**
     * Class ListMessage constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\Filter $filter
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Filter $filter,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroup = $filterGroup;
        $this->filter = $filter;
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context);
    }

    /**
     * Render my messages
     * @return \Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $ticketId = $this->getRequest()->getParam('id');
        $currentMessagesCount = $this->getRequest()->getParam('last_id', 0);
        $messages = $this->getMessages($ticketId);
        $lastId = $this->getLastId($messages);

        if ($lastId == $currentMessagesCount) {
            $response = ['html' => ''];
        } else {
            $layout = $resultPage->getLayout();
            $returnHtml = '';
            $block = $layout->getBlock(self::LIST_MESSAGE_BLOCK);
            if ($block) {
                $returnHtml .= $block->setIsNew(true)->setMessage(reset($messages))->toHtml();
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        "The \"%s\" block is not set for the handle \"%s\".",
                        self::LIST_MESSAGE_BLOCK,
                        self::LIST_MESSAGE_HANDLE
                    )
                );
            }
            $response = ['html' => $returnHtml, 'lastId' => $lastId, 'ticketId' => $ticketId];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setHttpResponseCode(200)->setData($response);
    }

    /**
     * Get the messages by ticket ID
     *
     * @param int $ticketId
     *
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    public function getMessages($ticketId)
    {
        if (!$this->messages && $ticketId) {
            $this->filter->setField('ticket_id')->setValue($ticketId);
            $this->filterGroup->setFilters([$this->filter]);
            $this->searchCriteria->setFilterGroups([$this->filterGroup]);
            $this->messages = $this->messageRepositoryInterface->getList($this->searchCriteria);
        }

        if (!is_array($this->messages)) {
            $this->messages = [];
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
