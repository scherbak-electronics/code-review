<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Model\Attachment\Source\ContentType;


/**
 * Class AddAttachments
 *
 * @package MageWorx\Downloads\Controller\Adminhtml\Attachment
 */
class AddAttachments extends Save
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data           = $this->getRequest()->getPost('attachment');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $data = $this->filterData($data);

            $totalAttachmentQty = $this->getTotalAttachmentQty($data);
            $savedAttachmentQty = 0;

            for ($i = 0; $i < $totalAttachmentQty; $i++) {

                $this->coreRegistry->unregister('mageworx_downloads_attachment');

                $attachment = $this->initAttachment();
                $attachment->addData($data);

                if ($attachment->getType() == ContentType::CONTENT_FILE) {
                    $finalFilename = $data['multifile'][$i]; // t/e/test.jpg -> t/e/test_3.jpg
                    $realFilename  = $data['realname'][$i];  // test.jpg

                    if ($finalFilename) {
                        $attachment->setFilename($finalFilename);
                        $attachment->setFiletype(substr($finalFilename, strrpos($finalFilename, '.') + 1));
                        $attachment->setUrl('');

                        $attachment->setSize(
                            filesize($this->fileLinkModel->getBaseDir() . $finalFilename)
                        );
                    }
                } elseif ($attachment->getContentType() == ContentType::CONTENT_URL) {
                    $attachment->setFilename('');
                    $attachment->setFiletype('');
                    $realFilename = $attachment->getUrl();
                } else {
                    throw new InputException(__('The attachment type is invalid. Verify and try again.'));
                }

                $attachment->setProductIds($this->getProductIds($data));
                $attachment->setStoreLocales(
                    $this->convertLocaleFormDataToObjects($data, $realFilename)
                );

                $this->_eventManager->dispatch(
                    'mageworx_downloads_attachment_prepare_save',
                    [
                        'attachment' => $attachment,
                        'request'    => $this->getRequest()
                    ]
                );

                try {
                    $this->attachmentRepository->save($attachment);
                    $savedAttachmentQty++;
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e,
                        __(
                            'Something went wrong while saving the attachment %1.',
                            $realFilename
                        )
                    );
                }
            }

            $this->_getSession()->setMageWorxDownloadsAttachmentData(false);

            $errorOccurred = (
                0 == $totalAttachmentQty
                || $totalAttachmentQty != $savedAttachmentQty
            );

            if ($errorOccurred) {

                $isRedirectToTheCreationPage = true;

                if (0 != $savedAttachmentQty) {
                    $this->messageManager->addWarningMessage(
                        __(
                            'Only %1 attachment(s) out of %2 have been saved.',
                            $savedAttachmentQty,
                            $totalAttachmentQty
                        )
                    );
                    // At this point better not to go back to the edit page. User must see which files were uploaded,
                    // and which were not.
                    $isRedirectToTheCreationPage = false;
                } else {
                    $this->_getSession()->setMageWorxDownloadsAttachmentData($data);

                    if (0 == $totalAttachmentQty) {
                        $this->messageManager->addErrorMessage(__('At least one file must be attached.'));
                    }
                }
            } else {
                if (1 == $savedAttachmentQty) {
                    $this->messageManager->addSuccessMessage(__('The attachment has been saved.'));
                } else {
                    $this->messageManager->addSuccessMessage(
                        __('%1 attachments have been saved.', $savedAttachmentQty)
                    );
                }
            }

            if (($errorOccurred && $isRedirectToTheCreationPage)
                || (!$errorOccurred && $this->getRequest()->getParam(
                        'back'
                    ))
            ) {
                $resultRedirect->setPath(
                    'mageworx_downloads/*/create',
                    [
                        '_current' => true
                    ]
                );
            } else {
                $resultRedirect->setPath('mageworx_downloads/*/');
            }
        }

        return $resultRedirect;
    }

    /**
     * @param array $data
     * @return int
     */
    protected function getTotalAttachmentQty(array $data): int
    {
        $result = 0;
        if (isset($data['type'])) {
            if ($data['type'] == ContentType::CONTENT_URL) {
                $result = 1;
            } elseif ($data['type'] == ContentType::CONTENT_FILE) {
                if (isset($data['multifile'])) {
                    $result = count($data['multifile']);
                }
            }
        }

        return $result;
    }
}
