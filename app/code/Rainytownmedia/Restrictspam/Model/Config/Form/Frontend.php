<?php
namespace Rainytownmedia\Restrictspam\Model\Config\Form;

class Frontend extends \Magento\Captcha\Model\Config\Form\AbstractForm
{
	protected $_configPath = 'captcha/frontend/areas';
	public function toOptionArray()
	{
        $optionArray = [];
        $backendConfig = $this->_config->getValue($this->_configPath, 'default');
        if ($backendConfig) {
            foreach ($backendConfig as $formName => $formConfig) {
				if( !in_array($formName, ['user_create', 'user_forgotpassword']) ) continue;
                if (!empty($formConfig['label'])) {
                    $optionArray[] = ['label' => $formConfig['label'], 'value' => $formName];
                }
            }
        }
        return $optionArray;
    }
}
