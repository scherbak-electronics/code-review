<?php
use \Magento\Store\Model\Store;
use \Magento\Store\Model\StoreManager;
use \Magento\Framework\App\Bootstrap;
require __DIR__ . '/../app/bootstrap.php';
$params = $_SERVER;
$bootstraps = Bootstrap::create(BP, $params);
$objectManager = $bootstraps->getObjectManager();
$app_state = $objectManager->get('\Magento\Framework\App\State');
$app_state->setAreaCode('frontend');

$_customerRegistry = $objectManager->create(\Magento\Customer\Model\CustomerRegistry::class);
$_customer = $objectManager->create(\Magento\Customer\Model\Customer::class);

$_customer = $_customerRegistry->retrieve(25409);
echo $_customer->getEmail();

$_customer->changePassword('Abc#1234')->save();
echo "done";