<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\ResourceModel;

use Cart2Quote\SalesRep\Api\Data\TypeSearchResultsInterfaceFactory;
use Cart2Quote\SalesRep\Model\ResourceModel\Type;
use Cart2Quote\SalesRep\Model\TypeFactory;
use Cart2Quote\SalesRep\Api\Data\TypeInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\InputException;

/**
 * Class TypeRepository
 * @package Cart2Quote\SalesRep\Model\ResourceModel
 */
class TypeRepository implements \Cart2Quote\SalesRep\Api\TypeRepositoryInterface
{
    /**
     * Type Factory
     *
     * @var \Cart2Quote\SalesRep\Model\TypeFactory
     */
    protected $typeFactory;

    /**
     * Type Resource Model
     *
     * @var \Cart2Quote\SalesRep\Model\ResourceModel\Type
     */
    protected $typeResourceModel;

    /**
     * Type Search Result Interface Factory
     *
     * @var TypeSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * Type Collection
     *
     * @var Type\Collection
     */
    protected $typeCollection;

    /**
     * TypeRepository constructor.
     *
     * @param \Cart2Quote\SalesRep\Model\TypeFactory $typeFactory
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\Type $typeResourceModel
     * @param TypeSearchResultsInterfaceFactory $typeSearchResultsInterfaceFactory
     * @param Type\Collection $typeCollection
     */
    public function __construct(
        \Cart2Quote\SalesRep\Model\TypeFactory $typeFactory,
        \Cart2Quote\SalesRep\Model\ResourceModel\Type $typeResourceModel,
        TypeSearchResultsInterfaceFactory $typeSearchResultsInterfaceFactory,
        \Cart2Quote\SalesRep\Model\ResourceModel\Type\Collection $typeCollection
    ) {
        $this->typeFactory = $typeFactory;
        $this->typeResourceModel = $typeResourceModel;
        $this->searchResultsFactory = $typeSearchResultsInterfaceFactory;
        $this->typeCollection = $typeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function save(TypeInterface $type)
    {
        $this->validate($type);

        if ($existingType = $this->getMainTypeByAssociatedId($type->getObjectId(), $type->getTypeId())) {
            $type->setId($existingType->getId());
        }

        $typeModel = $this->typeFactory->create();
        $typeModel->updateData($type);

        $this->typeResourceModel->save($typeModel);
        $typeModel->afterLoad();
        $type = $typeModel->getDataModel();

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($typeId)
    {
        $typeModel = $this->typeFactory->create();
        $this->typeResourceModel->load($typeModel, $typeId);
        $typeModel->afterLoad();

        return $typeModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->typeCollection;

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
        $types = [];
        /** @var \Cart2Quote\SalesRep\Model\Type $typeModel */
        foreach ($collection as $typeModel) {
            $types[] = $typeModel->getDataModel();
        }

        $searchResults->setItems($types);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(TypeInterface $type)
    {
        return $this->deleteById($type->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($typeId)
    {
        $type = $this->getById($typeId);
        $type->setDeleted(1);
        $this->save($type);
        return true;
    }

    /**
     * Validate quotation type attribute values.
     *
     * @param TypeInterface $type
     * @throws InputException
     * @throws \Exception
     *
     * @return void
     */
    protected function validate(TypeInterface $type)
    {
        $exception = new InputException();

        if (!\Laminas\Validator\StaticValidator::execute(trim($type->getTypeId()), 'NotEmpty')) {
            $exception->addError(__(InputException::requiredField('type_id')));
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
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
}
