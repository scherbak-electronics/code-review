<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Api;

/**
 * Interface TypeRepositoryInterface
 * @package Cart2Quote\SalesRep\Api
 */
interface TypeRepositoryInterface
{
    /**
     * Create type.
     *
     * @param \Cart2Quote\SalesRep\Api\Data\TypeInterface $quotationType
     * @api
     *
     * @return \Cart2Quote\SalesRep\Api\Data\TypeInterface
     */
    public function save(\Cart2Quote\SalesRep\Api\Data\TypeInterface $quotationType);

    /**
     * Retrieve type.
     *
     * @param int $quotationTypeId
     * @api
     *
     * @return \Cart2Quote\SalesRep\Api\Data\TypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($quotationTypeId);

    /**
     * Retrieve types which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @api
     *
     * @return []
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Type.
     *
     * @param \Cart2Quote\SalesRep\Api\Data\TypeInterface $type
     * @api
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Cart2Quote\SalesRep\Api\Data\TypeInterface $type);

    /**
     * Delete an type by ID.
     *
     * @param int $quotationTypeId
     * @api
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($quotationTypeId);
}
