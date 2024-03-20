<?php
namespace Rainytownmedia\Restrictspam\Helper;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
	const SPEED_ALLOW = 1000;//1000; //unit is ms, 1000ms = 1s
	const DETECT_FIELD_ALLOW = 500;//500; //unit is ms, 500ms = 0.5s
	#############
	const XML_PATH_RESTRICTSPAM = 'restrictspam/configuration/';
	
	public function getConfig($key, $store = null){
        return $this->scopeConfig->getValue(
            self::XML_PATH_RESTRICTSPAM . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
	
	public function isEnable(){
		return $this->getConfig('status');
	}
	
	public function getFormsEnable(){
		$forms = $this->getConfig('forms');
		return explode(',', $forms);
	}
	
	public function isRestrictspam($form_name){
		if($this->isEnable()){
			$forms = $this->getFormsEnable();
			if( in_array($form_name, $forms) ) return true;
		}
		return false;
	}
	#############
	public function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	public function test(){
		echo "<pre>";print_r(microtime());echo"</pre>";
		$time_start = $this->microtime_float();

		// Sleep for a while
		echo '--time start: ' .$time_start;

		usleep(5000);

		$time_end = $this->microtime_float();
		echo'--time end: '. $time_end.'<br>';

		$time = $time_end - $time_start;

		echo "Did nothing in $time seconds\n";
	}
}