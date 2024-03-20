<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Api\AttachmentRepositoryInterface;
use MageWorx\Downloads\Controller\Adminhtml\Attachment;
use Magento\Backend\App\Action\Context;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory;
use MageWorx\Downloads\Model\Attachment as AttachmentModel;

abstract class MassAction extends Attachment
{
    /**
     *
     * @var Filter
     */
    protected $filter;

    /**
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassAction constructor.
     *
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param AttachmentFactory $attachmentFactory
     * @param Context $context
     */
    public function __construct(
        AttachmentRepositoryInterface $attachmentRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Registry $registry,
        AttachmentFactory $attachmentFactory,
        Context $context
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($attachmentRepository, $registry, $attachmentFactory, $context);
    }

    /**
     * @param AttachmentModel $attachment
     * @return mixed
     */
    abstract protected function doTheAction(AttachmentModel $attachment);

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection     = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->count();

            foreach ($collection as $attachment) {
                $this->doTheAction($attachment);
            }
            $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $this->getErrorMessage());
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_downloads/*/index');

        return $redirectResult;
    }

    /**
     * @param $collectionSize
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize)
    {
        return __('Mass action successful on %1 records', $collectionSize);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('Mass action failed');
    }
}
