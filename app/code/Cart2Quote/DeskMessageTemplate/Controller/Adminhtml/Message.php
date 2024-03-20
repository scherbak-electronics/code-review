<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Result\PageFactory;
use Cart2Quote\DeskMessageTemplate\Api\MessageRepositoryInterface as MessageRepository;
use Cart2Quote\DeskMessageTemplate\Model\MessageFactory;

/**
 * Class Message
 * @package Cart2Quote\DeskMessageTemplate\Controller\Adminhtml
 */
class Message extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var MessageRepository
     */
    protected $messageRepository;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * Message constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param DataPersistorInterface $dataPersistor
     * @param MessageRepository $messageRepository
     * @param MessageFactory $messageFactory
     * @param Action\Context $context
     */
    public function __construct(
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        DataPersistorInterface $dataPersistor,
        MessageRepository $messageRepository,
        MessageFactory $messageFactory,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->dataPersistor = $dataPersistor;
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
    }

    /**
     * Render message template list view
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage->setActiveMenu('Cart2Quote_DeskMessageTemplate::desk_message_template');

        return $resultPage;
    }
}
