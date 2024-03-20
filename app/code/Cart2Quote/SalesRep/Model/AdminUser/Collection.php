<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\AdminUser;

/**
 * Class Collection
 * @package Cart2Quote\SalesRep\Model\AdminUser
 */
class Collection extends \Magento\User\Model\ResourceModel\User\Collection
{

    /**
     * Flag for add unassigned to option array.
     *
     * @var bool
     */
    protected $addUnassigned;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     * @param bool $addUnassigned
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        $addUnassigned = true
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->addUnassigned = $addUnassigned;
    }


    /**
     * Format collection to option array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];

        //This adds an empty row, so that users don't have to click 'Reset Filter'
        $optionArray[] = ['value' => '', 'label' => ' '];

        if ($this->getAddUnassigned()) {
            $optionArray[] = ['value' => 0, 'label' => __('Unassigned')];
        }

        foreach ($this as $user) {
            if ($user->getIsActive() == 1) {
				$optionArray[] = ['value' => $user->getId(), 'label' => $this->formatUser($user)];
			}
        }

        return $optionArray;
    }

    /**
     * Set to true to add the unassigned to the option array.
     *
     * @param bool $value
     * @return $this
     */
    public function setAddUnassigned($value)
    {
        $this->addUnassigned = $value;

        return $this;
    }

    /**
     * Get if unassigned is added to the option array.
     *
     * @return bool
     */
    public function getAddUnassigned()
    {
        return $this->addUnassigned;
    }

    /**
     * To String method for the admin user: Admin name - Admin email
     *
     * @param \Magento\User\Model\User $user
     * @return string
     */
    private function formatUser(\Magento\User\Model\User $user)
    {
        return "{$user->getName()}";
    }

}
