<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Model\Message;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Cart2Quote\DeskMessageTemplate\Model\Message;
use Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message\CollectionFactory;

/**
 * Class DataProvider
 * @package Cart2Quote\DeskMessageTemplate\Model\Message
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $messageCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $messageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $messageCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        /** @var Message $message */
        foreach ($items as $message) {
            $message->load($message->getId());
            $this->loadedData[$message->getMessageId()] = $message->getData();
        }
        $data = $this->dataPersistor->get('desk_message_template');

        if (!empty($data)) {
            $message = $this->collection->getNewEmptyItem();
            $message->setData($data);
            $this->loadedData[$message->getMessageId()] = $message->getData();
            $this->dataPersistor->clear('desk_message_template');
        }

        return $this->loadedData;
    }
}