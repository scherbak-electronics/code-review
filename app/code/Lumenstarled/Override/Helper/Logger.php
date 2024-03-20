<?php
namespace Lumenstarled\Override\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;

class Logger extends AbstractHelper
{
	public function addlog($data, $name)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . "/var/log/{$name}");
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($data);
	}
	
	public function addwarnlog($data, $name)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . "/var/log/{$name}");
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->warn($data);
	}
}