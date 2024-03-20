<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use MageWorx\Downloads\Api\AttachmentRepositoryInterface;
use MageWorx\Downloads\Api\Data\AttachmentInterface;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\Downloads\Controller\Adminhtml\Attachment as AttachmentController;
use Magento\Framework\Registry;
use MageWorx\Downloads\Model\Attachment;

class InlineEdit extends AttachmentController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * InlineEdit constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param AttachmentFactory $attachmentFactory
     * @param Context $context
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AttachmentRepositoryInterface $attachmentRepository,
        JsonFactory $jsonFactory,
        Registry $registry,
        AttachmentFactory $attachmentFactory,
        Context $context
    ) {
        parent::__construct($attachmentRepository, $registry, $attachmentFactory, $context);
        $this->jsonFactory           = $jsonFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error      = false;
        $messages   = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(['messages' => [__('Please correct the sent data.')], 'error' => true]);
        }

        $this->searchCriteriaBuilder->addFilter('attachment_id', array_keys($postItems), 'in');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $result         = $this->attachmentRepository->getList($searchCriteria);

        /** @var AttachmentInterface $attachment */
        foreach ($result->getItems() as $attachment) {

            try {
                $attachmentData = $this->filterData($postItems[$attachment->getId()]);
                $attachment->addData($attachmentData);
                $this->addLocaleToAttachment($attachment, $attachmentData);
                if ($attachment->getData('url')) {
                    $attachment->clearAttachment();
                }
                $this->attachmentRepository->save($attachment);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithAttachmentId($attachment, $e->getMessage());
                $error      = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithAttachmentId($attachment, $e->getMessage());
                $error      = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithAttachmentId(
                    $attachment,
                    __('Something went wrong while saving the page.')
                );
                $error      = true;
            }
        }

        return $resultJson->setData(
            [
                'messages' => $messages,
                'error'    => $error
            ]
        );
    }

    /**
     * @param AttachmentInterface $attachment
     * @param array $attachmentData
     */
    protected function addLocaleToAttachment($attachment, array $attachmentData)
    {
        if (array_key_exists('name', $attachmentData)) {
            $locales = $attachment->getStoreLocales();

            foreach ($locales as $locale) {
                if ($locale->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                    $locale->setStoreName($attachmentData['name']);
                }
            }
        }
    }

    /**
     * Add attachment id to error message
     *
     * @param Attachment $attachment
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithAttachmentId(Attachment $attachment, $errorText)
    {
        return '[Attachment ID: ' . $attachment->getId() . '] ' . $errorText;
    }
}
