<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml Cart2Quote Edit Form
 */
namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Left;

/**
 * Class Form
 * @package Cart2Quote\Desk\Block\Adminhtml\Edit\Left
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Ticket
     *
     * @var \Cart2Quote\Desk\Model\Ticket
     */
    protected $ticket = null;

    /**
     * FieldSet
     *
     * @var \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected $fieldset = null;

    /**
     * Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $dataHelper = null;

    /**
     * Customer Repository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Search criteria
     *
     * @var \Magento\Framework\Api\SearchCriteria
     */
    protected $searchCriteria;

    /**
     * Sort Order Builder
     *
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * Catalog product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Desk system store model
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * Priority data
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection
     */
    protected $priorityCollection = null;

    /**
     * User Collection
     *
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $userCollection = null;

    /**
     * Customer name generator
     *
     * @var \Magento\Customer\Api\CustomerNameGenerationInterface
     */
    protected $customerNameGenerationInterface;

    /**
     * Class Form constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\CustomerNameGenerationInterface $customerNameGenerationInterface
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority\Collection $priorityCollection
     * @param \Magento\User\Model\ResourceModel\User\Collection $userCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\CustomerNameGenerationInterface $customerNameGenerationInterface,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Cart2Quote\Desk\Helper\Data $dataHelper,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority\Collection $priorityCollection,
        \Magento\User\Model\ResourceModel\User\Collection $userCollection,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->customerRepository = $customerRepository;
        $this->customerNameGenerationInterface = $customerNameGenerationInterface;
        $this->searchCriteria = $searchCriteria;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->productFactory = $productFactory;
        $this->systemStore = $systemStore;
        $this->priorityCollection = $priorityCollection;
        $this->userCollection = $userCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare edit ticket form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->createForm();
        $this->dispatchLeftFormBeforeEvent($form);
        $this->addTicketFieldset($form)
            ->addCustomer()
            ->addStoreInfo()
            ->addAssignee()
            ->addPriority();

        $form->setUseContainer(true);
        if ($this->getTicket()) {
            $values = $this->getTicket()->getData();
        } else {
            $values = ['priority_id' => $this->dataHelper->getDefaultPriority()];
        }

        $form->setValues($values);
        $this->dispatchLeftFormAfterEvent($form);

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get ticket store name
     *
     * @return null|string
     */
    protected function getStoreName()
    {
        if ($this->getTicket()) {
            $storeId = $this->getTicket()->getStoreId();
            if ($storeId === null) {
                $deleted = __(" [deleted]");
                return nl2br($this->getTicket()->getStoreName()) . $deleted;
            }

            $store = $this->_storeManager->getStore($storeId);
            $name = [
                $store->getWebsite()->getName(),
                "&nbsp;&nbsp;&nbsp;" . $store->getGroup()->getName(),
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $store->getName()
            ];
            return implode('<br/>', $name);
        }

        return null;
    }

    /**
     * Get the ticket from registry
     *
     * @return mixed|null
     * @throws \Exception
     */
    protected function getTicket()
    {
        if (!$this->ticket) {
            $this->ticket = $this->_coreRegistry->registry('ticket_data');
        }

        return $this->ticket;
    }

    /**
     * Creates the ticket form
     *
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createForm()
    {
        return $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'ticket/*/save',
                        [
                            'id' => $this->getRequest()->getParam('id'),
                            'ret' => $this->_coreRegistry->registry('ret')
                        ]
                    ),
                    'method' => 'post'
                ],
            ]
        );
    }

    /**
     * Sets the fieldset locally
     *
     * @param \Magento\Framework\Data\Form $form
     * @return $this
     */
    protected function addTicketFieldset(\Magento\Framework\Data\Form $form)
    {
        if (!$this->fieldset) {
            $this->fieldset = $form->addFieldset(
                'ticket_edit_left_form',
                ['class' => 'ticket_edit_left_form']
            );
        }

        return $this;
    }

    /**
     * Retrieves the fieldset for the ticket fields
     *
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function getFieldset()
    {
        return $this->fieldset;
    }

    /**
     * Adds the customer information to the form.
     *
     * @return $this
     * @throws \Exception
     */
    protected function addCustomer()
    {
        $this->searchCriteria->setFilterGroups([])->setSortOrders(
            [
                $this->sortOrderBuilder
                    ->setField('firstname')
                    ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)->create(),
                $this->sortOrderBuilder
                    ->setField('lastname')
                    ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)->create()
            ]
        );

        $customerArray = $this->customerRepository->getList($this->searchCriteria);
        $customerGridOptionArray = [];
        foreach ($customerArray->getItems() as $customer) {
            $customerGridOptionArray[$customer->getId()] =
                $this->getCustomerName($customer) . ' - (' . $customer->getEmail() . ')';
        }

        $this->getFieldSet()->addField(
            'customer_id',
            'select',
            [
                'label' => __("Customer"),
                'required' => true,
                'name' => 'customer_id',
                'values' => $customerGridOptionArray,
            ]
        );

        return $this;
    }

    /**
     * Adds the priority to the form
     *
     * @return $this
     */
    protected function addPriority()
    {
        $this->getFieldSet()->addField(
            'priority_id',
            'select',
            [
                'label' => __("Priority"),
                'required' => true,
                'name' => 'priority_id',
                'values' => $this->priorityCollection->toGridOptionArray(),
            ]
        );
        return $this;
    }

    /**
     * Adds the priority to the form
     *
     * @return $this
     */
    protected function addAssignee()
    {
        $this->getFieldSet()->addField(
            'assignee_id',
            'select',
            [
                'label' => __("Assignee"),
                'required' => true,
                'name' => 'assignee_id',
                'values' => $this->getUserList()
            ]
        );
        return $this;
    }

    /**
     * Get a list of admin users
     *
     * @return array
     */
    protected function getUserList()
    {
        $this->userCollection->addOrder('firstname', 'ASC')->addOrder('lastname', 'ASC');

        $users = [0 => __("Unassigned")];
        foreach ($this->userCollection as $user) {
            $users[$user->getId()] = $this->formatUser($user);
        }

        return $users;
    }

    /**
     * To String method for the admin user: Admin name - Admin email
     *
     * @param \Magento\User\Model\User $user
     * @return string
     */
    protected function formatUser(\Magento\User\Model\User $user)
    {
        return "{$user->getName()} - {$user->getEmail()}";
    }

    /**
     * Adds the store info to this form
     *
     * @return $this
     */
    protected function addStoreInfo()
    {

        $this->getFieldSet()->addField(
            'store_id',
            'select',
            [
                'label' => __("Store"),
                'title' => __("Store"),
                'values' => $this->systemStore->getStoreValuesForForm(),
                'name' => 'store_id',
                'required' => true
            ]
        );

        return $this;
    }

    /**
     * Get the customer name
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return string
     */
    protected function getCustomerName(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        return $this->customerNameGenerationInterface->getCustomerName($customer);
    }

    /**
     * Dispatch before left form event
     *
     * @param \Magento\Framework\Data\Form $form
     * @return void
     */
    private function dispatchLeftFormBeforeEvent(
        \Magento\Framework\Data\Form $form
    ) {
        $this->_eventManager->dispatch(
            'desk_adminhtml_left_form_before',
            [
                'block' => $this,
                'form' => $form
            ]
        );
    }

    /**
     * Dispatch before left form event
     *
     * @param \Magento\Framework\Data\Form $form
     * @return void
     */
    private function dispatchLeftFormAfterEvent(
        \Magento\Framework\Data\Form $form
    ) {
        $this->_eventManager->dispatch(
            'desk_adminhtml_left_form_after',
            [
                'block' => $this,
                'form' => $form
            ]
        );
    }
}
