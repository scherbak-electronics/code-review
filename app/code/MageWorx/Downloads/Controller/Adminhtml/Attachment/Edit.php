<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action\Context;
use Magento\Store\Model\Store;
use MageWorx\Downloads\Api\AttachmentRepositoryInterface;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \MageWorx\Downloads\Controller\Adminhtml\Attachment
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param AttachmentFactory $attachmentFactory
     * @param Context $context
     */
    public function __construct(
        AttachmentRepositoryInterface $attachmentRepository,
        Registry $registry,
        PageFactory $resultPageFactory,
        AttachmentFactory $attachmentFactory,
        Context $context
    ) {
        parent::__construct($attachmentRepository, $registry, $attachmentFactory, $context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit attachment page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $attachment = $this->initAttachment();

            /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();

            $resultPage->setActiveMenu('MageWorx_Downloads::attachments');
            $resultPage->getConfig()->getTitle()->set((__('Attachment')));

            if ($attachment->getId()) {
                $title = __('Attachment "%1"', $attachment->getName(Store::DEFAULT_STORE_ID));
            } else {
                $title = __('New Attachment');
            }

            $resultPage->getConfig()->getTitle()->prepend($title);
            $data = $this->_getSession()->getData('mageworx_downloads_attachment_data', true);

            if (!empty($data)) {
                $attachment->setData($data);
            }

        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The section no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath(
                'mageworx_downloads/*/index'
            );

            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while loading the section page.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath(
                'mageworx_downloads/*/index'
            );

            return $resultRedirect;
        }

        return $resultPage;
    }
}
