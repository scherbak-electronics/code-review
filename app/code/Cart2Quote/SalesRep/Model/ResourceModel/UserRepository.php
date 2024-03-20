<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\ResourceModel;

use Cart2Quote\SalesRep\Api\Data\TicketSearchResultsInterfaceFactory;
use Cart2Quote\SalesRep\Model\ResourceModel\Ticket;
use Cart2Quote\SalesRep\Model\TicketFactory;
use Cart2Quote\SalesRep\Api\Data\UserInterface;
use Cart2Quote\SalesRep\Api\Data\UserSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\InputException;

/**
 * Class UserRepository
 * @package Cart2Quote\SalesRep\Model\ResourceModel
 */
class UserRepository implements \Cart2Quote\SalesRep\Api\UserRepositoryInterface
{
    /**
     * User Factory
     *
     * @var \Cart2Quote\SalesRep\Model\TypeFactory
     */
    protected $userFactory;

    /**
     * User Resource Model
     *
     * @var \Cart2Quote\SalesRep\Model\ResourceModel\User
     */
    protected $userResourceModel;

    /**
     * User Search Result Interface Factory
     *
     * @var UserSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * User Collection
     *
     * @var User\Collection
     */
    protected $userCollectionFactory;

    /**
     * UserRepository constructor.
     *
     * @param \Cart2Quote\SalesRep\Model\TypeFactory $userFactory
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\User $userResourceModel
     * @param UserSearchResultsInterfaceFactory $userSearchResultsInterfaceFactory
     * @param User\Collection $userCollection
     */
    public function __construct(
        \Cart2Quote\SalesRep\Model\TypeFactory $userFactory,
        \Cart2Quote\SalesRep\Model\ResourceModel\User $userResourceModel,
        UserSearchResultsInterfaceFactory $userSearchResultsInterfaceFactory,
        \Cart2Quote\SalesRep\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
    ) {
        $this->userFactory = $userFactory;
        $this->userResourceModel = $userResourceModel;
        $this->searchResultsFactory = $userSearchResultsInterfaceFactory;
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(UserInterface $user)
    {


        $this->validate($user);

        // die("test_save 89"); // worked..

        if ($existingUser = $this->getMainUserByAssociatedId($user->getObjectId(), $user->getTypeId())) {
            // echo "<pre>"; print_r($user->getTypeId());  die("condition 90"); // worked..
            $user->setId($existingUser->getId());
            // die("test ioio"); // worked..
        }

        $userModel = $this->userFactory->create();
        $userModel->updateData($user);

        // die("update user"); // worked..

        $this->userResourceModel->save($userModel);
        // die("save user"); // error here..
        $userModel->afterLoad();
        $user = $userModel->getDataModel();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($userId)
    {
        $userModel = $this->userFactory->create();
        $this->userResourceModel->load($userModel, $userId);
        $userModel->afterLoad();

        return $userModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->userCollectionFactory->create();

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $users = [];
        /** @var \Cart2Quote\SalesRep\Model\Ticket $userModel */
        foreach ($collection as $userModel) {
            $users[] = $userModel->getDataModel();
        }

        $searchResults->setItems($users);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(UserInterface $user)
    {
        return $this->deleteById($user->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($userId)
    {
        $userModel = $this->userFactory->create();
        $userModel->setId($userId);
        $this->userResourceModel->delete($userModel);
        return true;
    }

    /**
     * Validate quotation user attribute values.
     *
     * @param UserInterface $user
     * @throws InputException
     * @throws \Exception
     * @return void
     */
    protected function validate(UserInterface $user)
    {
        $exception = new InputException();

        if (!empty($user->getUserId()) && !\Laminas\Validator\StaticValidator::execute(trim($this->getValue($user->getUserId())), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('user_id')));
        }

        if (!empty($user->getUserId()) && !\Laminas\Validator\StaticValidator::execute(trim($this->getValue($user->getObjectId())), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('quote_id')));
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }

    /**
     * if object ID is null, return false.
     *
     * @param $param
     * @return mixed
     */
    public function getValue($param) {
        if (is_null($param)) {
            return false;
        }

        return $param;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     *
     * @return void
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $field = $filter->getField();
            $value = $filter->getValue();
            if (isset($field) && isset($value)) {
                $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $field;
                $conditions[] = [$conditionType => $value];
            }

        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * @param int $id
     * @param string $type
     * @return mixed
     */
    public function getMainUserByAssociatedId($id, $type)
    {
        $userModel = $this->userCollectionFactory->create()
            ->addFieldToFilter('object_id', $id)
            ->addFieldToFilter('type_id', $type)
            ->addFieldToFilter('is_main', 1)
            ->getFirstItem();

        $userModel->afterLoad();

        return $userModel->getDataModel();
    }
}
