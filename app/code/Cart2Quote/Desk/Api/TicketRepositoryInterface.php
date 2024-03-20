<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Api;

/**
 * Interface TicketRepositoryInterface
 * @package Cart2Quote\Desk\Api
 */
interface TicketRepositoryInterface
{
    /**
     * Create ticket.
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @api
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     */
    public function save(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket);

    /**
     * Retrieve ticket.
     *
     *
     * @param int $ticketId
     * @api
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($ticketId);

    /**
     * Retrieve ticket by quote id.
     *
     *
     * @param int $quoteId
     * @api
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByQuoteId($quoteId);

    /**
     * Retrieve tickets which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @api
     *
     * @return []
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete ticket.
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @api
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket);

    /**
     * Delete ticket by ID.
     *
     * @param int $ticketId
     * @api
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($ticketId);
}
