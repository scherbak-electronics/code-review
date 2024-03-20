<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use MageWorx\Downloads\Api\Data\SectionInterface;

interface SectionRepositoryInterface
{
    /**
     * Create or update section
     *
     * @param \MageWorx\Downloads\Api\Data\SectionInterface $section
     * @return \MageWorx\Downloads\Api\Data\SectionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Exception If something went wrong while creating the Section.
     */
    public function save(\MageWorx\Downloads\Api\Data\SectionInterface $section): SectionInterface;

    /**
     * Get section
     *
     * @param int $sectionId
     * @return \MageWorx\Downloads\Api\Data\SectionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($sectionId): \MageWorx\Downloads\Api\Data\SectionInterface;

    /**
     * Delete section
     *
     * @param int $sectionId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If no Section with the given ID can be found.
     * @throws \Exception If something went wrong while performing the delete.
     */
    public function deleteById($sectionId): bool;

    /**
     * Search Sections
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MageWorx\Downloads\Api\Data\SectionSearchResultsInterface containing Data\SectionInterface objects
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete section
     *
     * @param \MageWorx\Downloads\Api\Data\SectionInterface $section
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If no Section with the given ID can be found.
     * @throws \Exception If something went wrong while performing the delete.
     */
    public function delete(\MageWorx\Downloads\Api\Data\SectionInterface $section): bool;
}
