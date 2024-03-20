<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\Section;

use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\Downloads\Model\ResourceModel\Section as SectionResource;
use MageWorx\Downloads\Model\Section as SectionModel;
use MageWorx\Downloads\Api\Data\SectionInterfaceFactory;

class SectionRegistry
{
    /**
     * @var SectionResource
     */
    protected $sectionResource;

    /**
     * @var SectionInterfaceFactory
     */
    protected $sectionFactory;

    /**
     * Section models
     *
     * @var SectionModel[]
     */
    private $sectionRegistryById = [];

    /**
     * SectionRegistry constructor.
     *
     * @param SectionResource $sectionResource
     */
    public function __construct(
        SectionResource $sectionResource,
        SectionInterfaceFactory $sectionFactory
    ) {
        $this->sectionResource = $sectionResource;
        $this->sectionFactory  = $sectionFactory;
    }

    /**
     * Register Section Model to registry
     *
     * @param SectionModel $sectionModel
     * @return void
     */
    public function registerSection(SectionModel $sectionModel)
    {
        $this->sectionRegistryById[$sectionModel->getId()] = $sectionModel;
    }

    /**
     * Retrieve Section Model from registry given an id
     *
     * @param int $sectionId
     * @return SectionModel
     * @throws NoSuchEntityException
     */
    public function retrieveSection($sectionId)
    {
        if (isset($this->sectionRegistryById[$sectionId])) {
            return $this->sectionRegistryById[$sectionId];
        }

        $section = $this->sectionFactory->create();

        $this->sectionResource->load($section, $sectionId);
        if (!$section->getId()) {
            // section does not exist
            throw NoSuchEntityException::singleField('sectionId', $sectionId);
        }
        $this->sectionRegistryById[$section->getId()] = $section;

        return $section;
    }

    /**
     * Remove an instance of the Section Model from the registry
     *
     * @param int $sectionId
     * @return void
     */
    public function removeSection($sectionId)
    {
        unset($this->sectionRegistryById[$sectionId]);
    }
}
