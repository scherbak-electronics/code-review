<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Store\Model\Store;
use MageWorx\Downloads\Api\Data\AttachmentInterface;
use MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory;

class AttachmentRepository implements \MageWorx\Downloads\Api\AttachmentRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MageWorx\Downloads\Api\Data\AttachmentInterfaceFactory
     */
    protected $attachmentDataObjectFactory;

    /**
     * @var \MageWorx\Downloads\Model\AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var \MageWorx\Downloads\Model\Attachment\ContentValidator
     */
    protected $contentValidator;

    /**
     * @var \MageWorx\Downloads\Api\Data\File\ContentUploaderInterface
     */
    protected $fileContentUploader;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var CollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @var ResourceModel\Attachment
     */
    protected $attachmentResource;

    /**
     * @var \MageWorx\Downloads\Model\Attachment\AttachmentHandler
     */
    protected $attachmentHandler;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \MageWorx\Downloads\Api\Data\AttachmentSearchResultsInterfaceFactory
     */
    private $attachmentSearchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \MageWorx\Downloads\Model\ResourceModel\Attachment $attachmentResource,
        \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \MageWorx\Downloads\Api\Data\AttachmentInterfaceFactory $attachmentInterfaceFactory,
        \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory,
        \MageWorx\Downloads\Model\Attachment\ContentValidator $contentValidator,
        EncoderInterface $jsonEncoder,
        \MageWorx\Downloads\Api\Data\File\ContentUploaderInterface $fileContentUploader,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        \MageWorx\Downloads\Model\Attachment\AttachmentHandler $attachmentHandler,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \MageWorx\Downloads\Api\Data\AttachmentSearchResultsInterfaceFactory $attachmentSearchResultsFactory
    ) {
        $this->productRepository                = $productRepository;
        $this->attachmentDataObjectFactory      = $attachmentInterfaceFactory;
        $this->attachmentFactory                = $attachmentFactory;
        $this->contentValidator                 = $contentValidator;
        $this->jsonEncoder                      = $jsonEncoder;
        $this->fileContentUploader              = $fileContentUploader;
        $this->attachmentCollectionFactory      = $attachmentCollectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->attachmentResource               = $attachmentResource;
        $this->attachmentHandler                = $attachmentHandler;
        $this->collectionProcessor              = $collectionProcessor;
        $this->attachmentSearchResultsFactory   = $attachmentSearchResultsFactory;
    }

    /**
     * @param int $attachmentId
     * @return \MageWorx\Downloads\Api\Data\AttachmentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $attachmentId): \MageWorx\Downloads\Api\Data\AttachmentInterface
    {
        /** @var \MageWorx\Downloads\Api\Data\AttachmentInterface $attachment */
        $attachment = $this->attachmentFactory->create();
        $this->attachmentResource->load($attachment, $attachmentId);
        if (!$attachment->getId()) {
            throw new NoSuchEntityException(__('Attachment with specified ID "%1" not found.', $attachmentId));
        }

        return $attachment;
    }

    /**
     * @param AttachmentInterface $attachment
     * @return AttachmentInterface
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function save(AttachmentInterface $attachment): AttachmentInterface
    {
        if ($attachment->getId() !== null) {

            /** @var $existingLink \Magento\Downloadable\Model\Link */
            $existingAttachment = $this->getById($attachment->getId());
            if (!$existingAttachment->getId()) {
                throw new NoSuchEntityException(
                    __('No attachment with the provided ID was found. Verify the ID and try again.')
                );
            }
        }

        $this->prepareData($attachment);

        $this->validateAttachmentType($attachment);

        if (!$this->contentValidator->isValid($attachment, true)) {
            throw new InputException(__('The link information is invalid. Verify the link and try again.'));
        }

        return $this->saveAttachment($attachment);
    }

    /**
     * @param \MageWorx\Downloads\Api\Data\AttachmentInterface $attachment
     */
    protected function prepareData($attachment)
    {
        if ($attachment->getStoreId()
            && $attachment->getStoreIds()
            && array_search(Store::DEFAULT_STORE_ID, $attachment->getStoreIds()) !== false
        ) {
            $attachment->setStoreIds([Store::DEFAULT_STORE_ID]);
        }
    }

    /**
     * Construct Data structure and Save it.
     *
     * @param AttachmentInterface $attachment
     * @return \MageWorx\Downloads\Api\Data\AttachmentInterface
     */
    protected function saveAttachment(
        AttachmentInterface $attachment
    ) {
        $data = [
            'attachment_id'      => (int)$attachment->getId(),
            'is_delete'          => 0,
            'section_id'         => $attachment->getSectionId(),
            'type'               => $attachment->getType(),
            'filetype'           => $attachment->getFiletype(),
            'size'               => $attachment->getSize(),
            'downloads'          => $attachment->getDownloads(),
            'downloads_limit'    => $attachment->getDownloadsLimit(),
            'is_attach'          => $attachment->getIsAttach(),
            'store_ids'          => $attachment->getStoreIds(),
            'is_active'          => $attachment->getIsActive(),
            'customer_group_ids' => $attachment->getCustomerGroupIds(),
            'product_ids'        => $attachment->getProductIds(),
            'store_locales'      => $attachment->getStoreLocales(),
            'date_added'         => $attachment->getDateAdded()
        ];

        if ($attachment->getAttachmentFileContent() !== null) {
            $data['file'] = $this->jsonEncoder->encode(
                [
                    $this->fileContentUploader->upload($attachment->getAttachmentFileContent()),
                ]
            );
        } elseif ($attachment->getUrl()) {
            $data['url'] = $attachment->getUrl();
        } else {
            //existing link file
            $data['file'] = $this->jsonEncoder->encode(
                [
                    [
                        'file'   => $attachment->getFilename(),
                        'status' => 'old',
                    ]
                ]
            );
        }

        $downloadableData = ['attachment' => [$data]];

        return $this->attachmentHandler->save($downloadableData);
    }

    /**
     * @param AttachmentInterface $attachment
     * @return ResourceModel\Attachment
     * @throws \Exception
     */
    public function delete(\MageWorx\Downloads\Api\Data\AttachmentInterface $attachment)
    {
        return $this->attachmentResource->delete($attachment);
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id): bool
    {
        /** @var \MageWorx\Downloads\Api\Data\AttachmentInterface $attachment */
        $attachment = $this->getById($id);

        try {
            $this->attachmentResource->delete($attachment);
        } catch (\Exception $e) {
            throw new StateException(__('The attachment with "%1" ID can\'t be deleted.', $attachment->getId()), $e);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection $collection */
        $collection = $this->attachmentCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection);
        $this->collectionProcessor->process($searchCriteria, $collection);

        $attachments = [];

        /** @var \MageWorx\Downloads\Model\Attachment $attachmentModel */
        foreach ($collection as $attachmentModel) {
            $attachmentModel->loadRelations();
            $attachments[] = $attachmentModel;
        }

        return $this->attachmentSearchResultsFactory->create()
                                                    ->setItems($attachments)
                                                    ->setTotalCount($collection->getSize())
                                                    ->setSearchCriteria($searchCriteria);
    }

    /**
     * Check that Attachment type exist.
     *
     * @param AttachmentInterface $attachment
     * @return void
     * @throws InputException
     */
    private function validateAttachmentType(AttachmentInterface $attachment): void
    {
        if (!in_array($attachment->getType(), ['url', 'file'], true)) {
            throw new InputException(__('The attachment type is invalid. Verify and try again.'));
        }
    }
}
