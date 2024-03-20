<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Observer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\Downloads\Api\AttachmentRepositoryInterface;
use MageWorx\Downloads\Api\Data\AttachmentInterface;

class AssignAttachmentsToDefaultSectionObserver implements ObserverInterface
{
    /**
     * @var AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * AssignAttachmentsToDefaultSectionObserver constructor.
     *
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AttachmentRepositoryInterface $attachmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->attachmentRepository  = $attachmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $section = $observer->getEvent()->getObject();

        if ($section->getId() && $section->getId() != \MageWorx\Downloads\Model\Section::DEFAULT_ID) {

            $this->searchCriteriaBuilder->addFilter('section_id', $section->getId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $result         = $this->attachmentRepository->getList($searchCriteria);

            /** @var AttachmentInterface $attachment */
            foreach ($result->getItems() as $attachment) {
                $attachment->setSectionId(\MageWorx\Downloads\Model\Section::DEFAULT_ID);
                $this->attachmentRepository->save($attachment);
            }
        }
    }
}
