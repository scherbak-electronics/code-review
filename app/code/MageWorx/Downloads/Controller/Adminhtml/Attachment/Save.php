<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use MageWorx\Downloads\Api\Data\AttachmentLocaleInterfaceFactory;
use MageWorx\Downloads\Controller\Adminhtml\Attachment as AttachmentController;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Registry;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Model\Attachment\Link as FileLinkModel;
use MageWorx\Downloads\Model\Upload;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Framework\Filesystem;
use MageWorx\Downloads\Model\Attachment\Source\ContentType;
use MageWorx\Downloads\Model\Attachment\Source\AssignType;
use MageWorx\Downloads\Api\AttachmentRepositoryInterface;

class Save extends AttachmentController
{
    /**
     * Attachment factory
     *
     * @var \MageWorx\Downloads\Model\AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * File model
     *
     * @var FileLinkModel
     */
    protected $fileLinkModel;

    /**
     * Upload model
     *
     * @var \MageWorx\Downloads\Model\Upload
     */
    protected $uploadModel;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var AttachmentLocaleInterfaceFactory
     */
    protected $attachmentLocaleFactory;

    /**
     * Save constructor.
     *
     * @param AttachmentLocaleInterfaceFactory $attachmentLocaleFactory
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param ProductCollectionFactory $productCollectionFactory
     * @param JsHelper $jsHelper
     * @param FileLinkModel $fileLinkModel
     * @param Upload $uploadModel
     * @param Registry $registry
     * @param AttachmentFactory $attachmentFactory
     * @param Filesystem $fileSystem
     * @param Context $context
     */
    public function __construct(
        AttachmentLocaleInterfaceFactory $attachmentLocaleFactory,
        AttachmentRepositoryInterface $attachmentRepository,
        ProductCollectionFactory $productCollectionFactory,
        JsHelper $jsHelper,
        FileLinkModel $fileLinkModel,
        Upload $uploadModel,
        Registry $registry,
        AttachmentFactory $attachmentFactory,
        Filesystem $fileSystem,
        Context $context
    ) {
        parent::__construct($attachmentRepository, $registry, $attachmentFactory, $context);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->jsHelper                 = $jsHelper;
        $this->fileLinkModel            = $fileLinkModel;
        $this->uploadModel              = $uploadModel;
        $this->fileSystem               = $fileSystem;
        $this->attachmentRepository     = $attachmentRepository;
        $this->attachmentLocaleFactory  = $attachmentLocaleFactory;
    }

    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('attachment');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data       = $this->prepareData($data);
            $attachment = $this->initAttachment($data);
            $attachment->addData($data);

            try {
                if ($attachment->getType() == ContentType::CONTENT_FILE) {
                    $file = $this->uploadModel->uploadFileAndGetName(
                        'filename',
                        $this->fileLinkModel->getBaseDir(),
                        $data
                    );

                    if ($file) {
                        $attachment->setFilename($file);
                        $attachment->setFiletype(substr($file, strrpos($file, '.') + 1));
                        $attachment->setUrl('');
                        $attachment->setSize(filesize($this->fileLinkModel->getBaseDir() . $file));
                    }
                } elseif ($attachment->getContentType() == ContentType::CONTENT_URL) {
                    $attachment->setFilename('');
                    $attachment->setFiletype('');
                }

                $attachment->setProductIds($this->getProductIds($data));
                $attachment->setStoreLocales($this->convertLocaleFormDataToObjects($data));

                $this->_eventManager->dispatch(
                    'mageworx_downloads_attachment_prepare_save',
                    [
                        'attachment' => $attachment,
                        'request'    => $this->getRequest()
                    ]
                );

                $this->attachmentRepository->save($attachment);

                $this->messageManager->addSuccessMessage(__('The attachment has been saved.'));
                $this->_getSession()->setMageWorxDownloadsAttachmentData(false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageworx_downloads/*/edit',
                        [
                            'attachment_id' => $attachment->getId(),
                            '_current'      => true
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageworx_downloads/*/');

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_getSession()->setMageWorxDownloadsAttachmentData($data);

            $resultRedirect->setPath(
                'mageworx_downloads/*/edit',
                [
                    'attachment_id' => $attachment->getId(),
                    '_current'      => true
                ]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('mageworx_downloads/*/');

        return $resultRedirect;
    }

    /**
     * @param array $data
     * @param string|null $filename
     * @return \MageWorx\Downloads\Api\Data\AttachmentLocaleInterface[]
     */
    protected function convertLocaleFormDataToObjects(array $data, ?string $filename = null): array
    {
        $storeLabels = [];

        foreach ($data['store_attachment_names'] as $storeId => $name) {
            $storeLabelObj = $this->attachmentLocaleFactory->create();

            $name = ((int)$storeId === 0 && !$name) ? $filename : $name;

            $storeLabelObj->setStoreId($storeId);
            $storeLabelObj->setStoreName($name);
            $storeLabelObj->setStoreDescription($data['store_attachment_descriptions'][$storeId]);
            $storeLabels[] = $storeLabelObj;
        }

        return $storeLabels;
    }

    /**
     *
     * @param array $data
     * @return array|null
     */
    protected function getProductIds(array $data)
    {
        $productIds = null;

        if ($data['assign_type'] == AssignType::ASSIGN_BY_GRID) {
            $products = $this->getRequest()->getPost('products', -1);
            if ($products != -1) {
                $productIds = $this->jsHelper->decodeGridSerializedInput($products);
            }
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_IDS) {
            $productIds = $this->convertMultiStringToArray($data['productids'], 'intval');
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_SKUS) {
            $productSkus = $this->convertMultiStringToArray($data['productskus']);

            if ($productSkus) {
                $collection = $this->productCollectionFactory->create();
                $collection->addFieldToFilter('sku', ['in' => $productSkus]);
                $productIds = array_map('intval', $collection->getAllIds());
            }
        }

        return $productIds;
    }

    /**
     *
     * @param string $string
     * @param string $finalFunction
     * @return array
     */
    protected function convertMultiStringToArray($string, $finalFunction = null)
    {
        if (!trim($string)) {
            return [];
        }

        $rawLines = array_filter(preg_split('/\r?\n/', $string));
        $rawLines = array_map('trim', $rawLines);
        $lines    = array_filter($rawLines);

        if (!$lines) {
            return [];
        }

        $array = [];
        foreach ($lines as $line) {
            $rawIds  = explode(',', $line);
            $rawIds  = array_map('trim', $rawIds);
            $lineIds = array_filter($rawIds);
            if (!$finalFunction) {
                $lineIds = array_map($finalFunction, $lineIds);
            }
            $array = array_merge($array, $lineIds);
        }

        return $array;
    }

    /**
     * Prepares specific data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        if (empty($data['store_ids']) || array_search(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $data['store_ids']) !== false) {
            $data['store_ids'] = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        return $data;
    }
}
