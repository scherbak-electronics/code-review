<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Cart2Quote\DeskMessageTemplate\Api\MessageRepositoryInterface as MessageRepository;
use Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message\CollectionFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassActions
 * @package Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message
 */
abstract class MassActions extends Action
{
    /**
     * @var string
     */
    private $redirectUrl = '*/*/index';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var MessageRepository
     */
    protected $messageRepository;

    /**
     * MassActions constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param MessageRepository $messageRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        MessageRepository $messageRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->messageRepository = $messageRepository;
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath($this->redirectUrl);
    }

    /**
     * Execute action to collection items
     *
     * @param CollectionFactory $collection
     * @return ResponseInterface|ResultInterface
     */
    abstract protected function massAction($collection);
}
