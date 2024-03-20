<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Api;

/**
 * Interface OrderRepositoryInterface
 * @package Cart2Quote\SalesRep\Api
 */
interface OrderRepositoryInterface
{
    /**
     * Create order.
     *
     * @param \Cart2Quote\SalesRep\Api\Data\OrderInterface $order
     * @api
     *
     * @return \Cart2Quote\SalesRep\Api\Data\OrderInterface
     */
    public function save(\Cart2Quote\SalesRep\Api\Data\OrderInterface $order);

    /**
     * Retrieve order.
     *
     * @param int $orderId
     * @api
     *
     * @return \Cart2Quote\SalesRep\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If order with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($orderId);

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
    public function delete(\Cart2Quote\SalesRep\Api\Data\OrderInterface $type);

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
