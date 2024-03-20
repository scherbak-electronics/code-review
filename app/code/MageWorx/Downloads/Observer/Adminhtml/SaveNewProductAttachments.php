<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Observer\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Registry;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Api\Data\AttachmentLocaleInterfaceFactory;
use MageWorx\Downloads\Model\Attachment\Source\ContentType;
use MageWorx\Downloads\Model\AttachmentFactory;

class SaveNewProductAttachments implements ObserverInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * Attachment factory
     *
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var bool
     */
    protected $hasRequiredData = false;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var AttachmentLocaleInterfaceFactory
     */
    protected $attachmentLocaleFactory;

    /**
     * SaveNewProductAttachments constructor.
     *
     * @param Context $context
     * @param AttachmentFactory $attachmentFactory
     * @param Registry $coreRegistry
     * @param AttachmentLocaleInterfaceFactory $attachmentLocaleFactory
     */
    public function __construct(
        Context $context,
        AttachmentFactory $attachmentFactory,
        Registry $coreRegistry,
        AttachmentLocaleInterfaceFactory $attachmentLocaleFactory
    ) {
        $this->context                 = $context;
        $this->messageManager          = $context->getMessageManager();
        $this->attachmentFactory       = $attachmentFactory;
        $this->coreRegistry            = $coreRegistry;
        $this->attachmentLocaleFactory = $attachmentLocaleFactory;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $product = $this->coreRegistry->registry('product');
        $data    = $this->context->getRequest()->getPostValue('new_attachments', -1);

        $this->checkRequiredData($data);

        if ($product && $product->getId() && $this->hasRequiredData) {
            $productId = $product->getId();
            $data      = $this->prepareData($data);

            $totalAttachmentQty = $this->getTotalAttachementsQuantity($data);

            for ($i = 0; $i < $totalAttachmentQty; $i++) {

                $attachment = $this->attachmentFactory->create();

                $attachment->addData($data);

                if ($attachment->getType() == ContentType::CONTENT_FILE) {

                    $file               = $data['multifile'][$i]['file'];
                    $attachmentNickname = $data['multifile'][$i]['name'];
                    $size               = $data['multifile'][$i]['size'];

                    $attachment->setFilename($file);
                    $attachment->setFiletype(substr($file, strrpos($file, '.') + 1));
                    $attachment->setUrl('');
                    $attachment->setSize($size);

                } elseif ($attachment->getContentType() == ContentType::CONTENT_URL) {
                    $attachmentNickname = $attachment->getUrl();

                    $attachment->setFilename('');
                    $attachment->setFiletype('');
                } else {
                    throw new InputException(__('The attachment type is invalid. Verify and try again.'));
                }

                $attachment->setStoreLocales(
                    $this->convertLocaleFormDataToObjects(
                        [
                            'store_attachment_names'        => $this->context->getRequest()->getPostValue(
                                'store_attachment_names',
                                []
                            ),
                            'store_attachment_descriptions' => $this->context->getRequest()->getPostValue(
                                'store_attachment_descriptions',
                                []
                            ),
                        ],
                        $attachmentNickname
                    )
                );

                $attachment->setProductIds([$productId]);

                try {
                    $attachment->getResource()->save($attachment);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e,
                        __('Something went wrong while saving the attachment %1.', $attachmentNickname)
                    );
                }
            }
        }

        return $this;
    }

    /**
     * @param $data
     * @return int
     */
    protected function getTotalAttachementsQuantity($data)
    {
        if ($data['type'] == ContentType::CONTENT_URL) {
            return 1;
        }

        return count($data['multifile']);
    }

    /**
     * Prepares specific data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (array_search(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $data['store_ids']) !== false) {
            $data['store_ids'] = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        return $data;
    }

    /**
     * @param $data
     */
    protected function checkRequiredData($data)
    {
        if ($data != -1
            && isset($data['customer_group_ids'])
            && isset($data['store_ids'])
            && (isset($data['multifile']) || $data['url'] !== '')
        ) {
            $this->hasRequiredData = true;
        } else {
            $this->hasRequiredData = false;
        }
    }

    /**
     * @param array $data
     * @param string|null $filename
     * @return AttachmentLocaleInterface[]
     */
    protected function convertLocaleFormDataToObjects(array $data, ?string $filename = null): array
    {
        $storeLabels = [];

        foreach ($data['store_attachment_names'] as $storeId => $name) {
            /** @var \MageWorx\Downloads\Api\Data\AttachmentLocaleInterface $storeLabelObj */
            $storeLabelObj = $this->attachmentLocaleFactory->create();

            if ((int)$storeId === 0 && !$name) {
                $name = $filename;
            }

            $storeLabelObj->setStoreId($storeId);
            $storeLabelObj->setStoreName($name);
            $storeLabelObj->setStoreDescription($data['store_attachment_descriptions'][$storeId]);
            $storeLabels[] = $storeLabelObj;
        }

        return $storeLabels;
    }
}
