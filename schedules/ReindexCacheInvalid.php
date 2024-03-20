<?php
if (php_sapi_name() !== "cli") {
    die("This script run only cli");
}

ini_set('display_errors', true);
use \Magento\Store\Model\Store;
use \Magento\Store\Model\StoreManager;
use \Magento\Framework\App\Bootstrap;

require __DIR__ . '/../app/bootstrap.php';

$params = $_SERVER;
/* you can out your store id here */
$params[StoreManager::PARAM_RUN_CODE] = 'admin';
$params[Store::CUSTOM_ENTRY_POINT_PARAM] = true;
// add bootstrap
$bootstraps = Bootstrap::create(BP, $params);
$objectManager = $bootstraps->getObjectManager();
$app_state = $objectManager->get('\Magento\Framework\App\State');
$app_state->setAreaCode('crontab');

$processor = $objectManager->get('\Magento\Indexer\Model\Processor');
/* Regenerate indexes for all indexers */
/* Regenerate indexes for all invalid indexers */
$processor->reindexAllInvalid();

// this clean cache invalidated
$cacheTypeList = $objectManager->get(\Magento\Framework\App\Cache\TypeListInterface::class);
$invalidcache = $cacheTypeList->getInvalidated();
foreach($invalidcache as $key => $value) {
	$cacheTypeList->cleanType($key);
}