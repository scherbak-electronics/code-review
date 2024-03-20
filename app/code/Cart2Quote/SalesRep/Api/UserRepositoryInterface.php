<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Api;

/**
 * Interface UserRepositoryInterface
 * @package Cart2Quote\SalesRep\Api
 */
interface UserRepositoryInterface
{
    /**
     * Create user.
     *
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterface $quotationUser
     * @api
     *
     * @return \Cart2Quote\SalesRep\Api\Data\UserInterface
     */
    public function save(\Cart2Quote\SalesRep\Api\Data\UserInterface $quotationUser);

    /**
     * Retrieve user.
     *
     *
     * @param int $quotationUserId
     * @api
     *
     * @return \Cart2Quote\SalesRep\Api\Data\UserInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($quotationUserId);

    /**
     * Retrieve Main User by Associated ID.
     *
     *
     * @param int $id
     * @param string $type
     * @return Data\UserInterface
     * @api
     */
    public function getMainUserByAssociatedId($id, $type);

    /**
     * Retrieve users which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @api
     *
     * @return []
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete User.
     *
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterface $user
     * @api
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Cart2Quote\SalesRep\Api\Data\UserInterface $user);

    /**
     * Delete an user by ID.
     *
     * @param int $quotationUserId
     * @api
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($quotationUserId);
}
