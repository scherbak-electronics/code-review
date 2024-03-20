<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use MageWorx\Downloads\Api\AttachmentRepositoryInterface;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Framework\Registry;

abstract class Attachment extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_Downloads::attachments';

    /**
     * Attachment factory
     *
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /**
     * Attachment constructor.
     *
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param Registry $registry
     * @param AttachmentFactory $attachmentFactory
     * @param Context $context
     */
    public function __construct(
        AttachmentRepositoryInterface $attachmentRepository,
        Registry $registry,
        AttachmentFactory $attachmentFactory,
        Context $context
    ) {
        parent::__construct($context);
        $this->coreRegistry          = $registry;
        $this->attachmentFactory     = $attachmentFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->attachmentRepository  = $attachmentRepository;
    }

    /**
     * @param array|null $data
     * @return \MageWorx\Downloads\Api\Data\AttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function initAttachment(array $data = null)
    {
        if (!empty($data['attachment_id'])) {
            $attachmentId = $data['attachment_id'];
        } else {
            $attachmentId = $this->getRequest()->getParam('attachment_id');
        }

        if ($attachmentId) {
            $attachment = $this->attachmentRepository->getById($attachmentId);
        } else {
            $attachment = $this->attachmentFactory->create();
        }
        $this->coreRegistry->register('mageworx_downloads_attachment', $attachment);

        return $attachment;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    protected function filterData($data)
    {
        return $data;
    }
}
