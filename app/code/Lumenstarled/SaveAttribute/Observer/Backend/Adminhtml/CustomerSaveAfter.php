<?php
/**
 * Copyright © Rainnytownmedia All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lumenstarled\SaveAttribute\Observer\Backend\Adminhtml;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
class CustomerSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    
    protected $_customerRepository;
    protected $_resourceConnection;
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface  $_customerRepository,
         \Magento\Framework\App\ResourceConnection $resourceConnection

    ) {
        $this->_customerRepository = $_customerRepository;
        $this->_resourceConnection = $resourceConnection;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
       /// $customerId = $this->getCurrentCustomerId($observer->getRequest());
        $customer = $observer->getCustomer();        
        $param= $observer->getRequest()->getParam('customer');
        if ($customer->getId() && 
            $param['show_price']==1) {
             try{
                 $this->_connection = $this->_resourceConnection->getConnection();
             $customer_id =$customer->getId();
             $query = 'SELECT * FROM customer_entity_int where attribute_id=325 and entity_id='.$customer_id ;
             $results =$this->_connection->fetchCol($query);
             if(!$results||count($results)==0){
                $query ="                      
               INSERT INTO `customer_entity_int` (attribute_id,entity_id,value)  value(325,'$customer_id',1)  
                 "; 
               $this->_connection->query($query);  
             }
                              
             }catch(exception $ex){
                 die($ex->getMessage());
             }
             
        }
    }
}

