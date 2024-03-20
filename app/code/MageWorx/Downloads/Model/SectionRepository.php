<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Api\Data\SectionInterface;
use MageWorx\Downloads\Model\Section\SectionRegistry;
use MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory as SectionCollectionFactory;

class SectionRepository implements \MageWorx\Downloads\Api\SectionRepositoryInterface
{
    /**
     * @var ResourceModel\Section
     */
    protected $sectionResource;

    /**
     * @var SectionCollectionFactory
     */
    protected $sectionCollectionFactory;

    /**
     * Section registry
     *
     * @var  \MageWorx\Downloads\Model\Section\SectionRegistry
     */
    protected $sectionRegistry;

    /**
     * @var \MageWorx\Downloads\Api\Data\SectionSearchResultsInterfaceFactory
     */
    private $sectionSearchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * SectionRepository constructor.
     *
     * @param ResourceModel\Section $sectionResource
     * @param SectionCollectionFactory $sectionCollectionFactory
     * @param SectionRegistry $sectionRegistry
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \MageWorx\Downloads\Api\Data\SectionSearchResultsInterfaceFactory $sectionSearchResultsFactory
     */
    public function __construct(
        \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource,
        SectionCollectionFactory $sectionCollectionFactory,
        SectionRegistry $sectionRegistry,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \MageWorx\Downloads\Api\Data\SectionSearchResultsInterfaceFactory $sectionSearchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->sectionCollectionFactory         = $sectionCollectionFactory;
        $this->sectionResource                  = $sectionResource;
        $this->sectionRegistry                  = $sectionRegistry;
        $this->collectionProcessor              = $collectionProcessor;
        $this->sectionSearchResultsFactory      = $sectionSearchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function get($sectionId): SectionInterface
    {
        return $this->sectionRegistry->retrieveSection($sectionId);
    }

    /**
     * {@inheritdoc}
     */
    public function save(\MageWorx\Downloads\Api\Data\SectionInterface $section): SectionInterface
    {
        if ($section->getId()) {
            $this->sectionRegistry->retrieveSection($section->getId());
        }

        try {
            $this->sectionResource->save($section);
            $this->sectionRegistry->registerSection($section);
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(
                __('Could not save the section: %1', $exception->getMessage()),
                $exception
            );
        }

        return $section;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($sectionId): bool
    {
        $sectionModel = $this->sectionRegistry->retrieveSection($sectionId);
        $this->delete($sectionModel);
        $this->sectionRegistry->removeSection($sectionId);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\MageWorx\Downloads\Api\Data\SectionInterface $section): bool
    {
        $this->sectionResource->delete($section);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \MageWorx\Downloads\Model\ResourceModel\Section\Collection $collection */
        $collection = $this->sectionCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection);
        $this->collectionProcessor->process($searchCriteria, $collection);

        $sections = [];

        /** @var \MageWorx\Downloads\Model\Section $sectionModel */
        foreach ($collection as $sectionModel) {
            $sectionModel->loadRelations();
            $sections[] = $sectionModel;
        }

        return $this->sectionSearchResultsFactory->create()
                                                 ->setItems($sections)
                                                 ->setTotalCount($collection->getSize())
                                                 ->setSearchCriteria($searchCriteria);
    }
}
