<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml;

/**
 * Class Edit
 * @package Cart2Quote\Desk\Block\Adminhtml
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Ticket action pager
     *
     * @var \Magento\Review\Helper\Action\Pager
     */
    protected $ticketActionPager = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Ticket Repository
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * Ticket Model
     *
     * @var \Cart2Quote\Desk\Model\Ticket
     */
    protected $ticket;

    /**
     * Status data
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection
     */
    protected $statusCollection = null;

    /**
     * Class Edit constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Model\Ticket $ticket
     * @param \Magento\Review\Helper\Action\Pager $ticketActionPager
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection $statusCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Model\Ticket $ticket,
        \Magento\Review\Helper\Action\Pager $ticketActionPager,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection $statusCollection,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->ticketActionPager = $ticketActionPager;
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->ticket = $ticket;
        $this->statusCollection = $statusCollection;
        parent::__construct($context, $data);
    }

    /**
     * Overwrite: Force the form from layout xml file
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $form = $this->getLayout()->getBlock('form');
        $this->setChild('form', $form);
        parent::_prepareLayout();
    }

    /**
     * Initialize edit ticket
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'Cart2Quote_Desk';
        $this->_controller = 'adminhtml';

        $this->removeButton('reset');
        $this->removeButton('save');

        /** @var $actionPager \Magento\Review\Helper\Action\Pager */
        $actionPager = $this->ticketActionPager;
        $actionPager->setStorageId('tickets');

        $ticketId = $this->registerTicket();
        $this->addPreviousButton($actionPager, $ticketId);
        $this->addNextButton($actionPager, $ticketId);
        $this->addSubmitButton($ticketId);
        $this->addDeleteButton();
    }

    /**
     * Get edit ticket header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $ticketData = $this->coreRegistry->registry('ticket_data');
        if ($ticketData && $ticketData->getId()) {
            return __("Edit Ticket #").$ticketData->getId();
        } else {
            return __("New Ticket");
        }
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @param int $ticketId
     * @return array
     */
    protected function getAddProductButtonOptions($ticketId)
    {
        $splitButtonOptions = [];

        foreach ($this->statusCollection->toOptionArray() as $statusId => $status) {
            $onclick =
                "document.getElementById('edit_form').action = " .
                "'{$this->getTicketSubmitUrl($ticketId, $status['value'])}';".
                " document.getElementById('edit_form').submit();";

            $splitButtonOptions[$statusId] = [
                'label' => __("Submit as %1", ucfirst($status['label'])),
                'onclick' => $onclick,
                'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE == $status['value'],
            ];
        }

        return $splitButtonOptions;
    }

    /**
     * Get the Ticket submit url by specified status type
     *
     * @param int $ticketId
     * @param int $statusId
     * @return string
     */
    protected function getTicketSubmitUrl($ticketId, $statusId)
    {
        return $this->getUrl(
            '*/*/save',
            ['id' => $ticketId, 'status_id' => $statusId]
        );
    }

    /**
     * Register the ticket
     *
     * @return int $ticketId
     */
    protected function registerTicket()
    {
        $ticketId = $this->getRequest()->getParam('id');
        if ($ticketId) {
            $this->ticket->updateData($this->ticketRepositoryInterface->getById($ticketId));
            $this->coreRegistry->register('ticket_data', $this->ticket);
            return $ticketId;
        }

        return $ticketId;
    }

    /**
     * Add the previous ticket button
     *
     * @param \Magento\Review\Helper\Action\Pager $actionPager
     * @param int $ticketId
     *
     * @return void
     */
    protected function addPreviousButton(\Magento\Review\Helper\Action\Pager $actionPager, $ticketId)
    {
        $prevId = $actionPager->getPreviousItemId($ticketId);
        if ($prevId !== false) {
            $this->addButton(
                'previous',
                [
                    'label' => __("Previous"),
                    'onclick' => 'setLocation(\'' . $this->getUrl('desk/*/*', ['id' => $prevId]) . '\')'
                ],
                3,
                10
            );
        }
    }

    /**
     * Add the next ticket button
     *
     * @param \Magento\Review\Helper\Action\Pager $actionPager
     * @param int $ticketId
     *
     * @return void
     */
    protected function addNextButton(\Magento\Review\Helper\Action\Pager $actionPager, $ticketId)
    {
        $nextId = $actionPager->getNextItemId($ticketId);
        if ($nextId !== false) {
            $this->addButton(
                'next',
                [
                    'label' => __("Next"),
                    'onclick' => 'setLocation(\'' . $this->getUrl('desk/*/*', ['id' => $nextId]) . '\')'
                ],
                3,
                105
            );
        }
    }

    /**
     * Add the submit button
     *
     * @param int $ticketId
     *
     * @return void
     */
    protected function addSubmitButton($ticketId)
    {
        $statusHtml = __("Open");
        if ($this->ticket->getStatus()) {
            $statusHtml = $this->getLabelHtml($this->ticket->getStatus());
        }

        $addButtonProps = [
            'id' => 'submit_ticket',
            'label' => __("Submit as %1", $statusHtml),
            'class' => 'add',
            'button_class' => '',
            'class_name' => 'Cart2Quote\Desk\Block\Adminhtml\Edit\Overwrite\SplitButton',
            'options' => $this->getAddProductButtonOptions($ticketId),
        ];
        $this->buttonList->add('add_new', $addButtonProps);
    }

    /**
     * Add the delete button
     *
     * @return void
     */
    protected function addDeleteButton()
    {
        $this->buttonList->update('delete', 'label', __("Delete Ticket"));
        if ($this->getRequest()->getParam('ret', false) == 'pending') {
            $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $this->getUrl('catalog/*/pending') . '\')');
            $this->buttonList->update(
                'delete',
                'onclick',
                'deleteConfirm(' . '\'' . __(
                    'Are you sure you want to do this?'
                ) . '\' ' . '\'' . $this->getUrl(
                    '*/*/delete',
                    [$this->_objectId => $this->getRequest()->getParam($this->objectId), 'ret' => 'pending']
                ) . '\'' . ')'
            );
            $this->coreRegistry->register('ret', 'pending');
        }
    }

    /**
     * Return label formatted for HTML
     *
     * @param string $label The label
     * @return string
     */
    public function getLabelHtml($label)
    {
        return $this->escapeHtml(ucfirst($label));
    }
}
