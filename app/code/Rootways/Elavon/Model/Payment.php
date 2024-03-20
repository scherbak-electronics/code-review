<?php
/**
 * Elavon Payment Model.
 *
 * @category  Payment Integration
 * @package   Rootways_Elavon
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/shop/media/extension_doc/license_agreement.pdf
 */


namespace Rootways\Elavon\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Monolog\Logger;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\Exception\LocalizedException;

class Payment extends \Magento\Payment\Model\Method\Cc
{
    const CODE = 'rootways_elavon_option';
    protected $_code = self::CODE;
    protected $_formBlockType = 'Rootways\Elavon\Block\Form';
    protected $_infoBlockType = 'Rootways\Elavon\Block\Info';
 
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;
    protected $_canReviewPayment = false;
    protected $_canCancelInvoice = true;
    
    /**
     * Payment Model.
     * @param Magento\Framework\Model\Context $context
     * @param Magento\Framework\Registry $registry
     * @param Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param Magento\Payment\Helper\Data $paymentData
     * @param Rootways\Elavon\Helper\Data $customHelper
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Magento\Payment\Model\Method\Logger $logger
     * @param Magento\Framework\Module\ModuleListInterface $moduleList
     * @param Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Rootways\Elavon\Helper\Data $customHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = array()
    ) {
        
        $this->customHelper = $customHelper;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }
    
    public function isAvailable(CartInterface $sp762271 = null)
    {
        return true;
    }
    
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($amount <= 0) {
            throw new LocalizedException(__('Invalid amount for capture.'));
        }
        $ref_id = 0;
        $trn_type = 'a';
        $order = $payment->getOrder();
        $result = $this->createCharge($payment,$amount,$trn_type,$ref_id);
        if ($result->ssl_result != '0') {
            if (isset($result->ssl_result_message)) {
                $errorMessage = $result->ssl_result_message;
            } else {
                $errorMessage = $result->errorMessage;
            }
            throw new LocalizedException(__('Error Processing the request. '.$errorMessage));
        } else {
            if ($result->ssl_result == '0') {
                $payment->setTransactionId($result->ssl_txn_id);
                $payment->setLastTransId($result->ssl_txn_id);
                $payment->setIsTransactionClosed(0)->setAdditionalInformation('real_transaction_id', (string)$result->ssl_txn_id);
                $payment->setAdditionalInformation('order_tra_id', (string)$result->ssl_txn_id);
                $payment->setAdditionalInformation('avs_res_id', (string)$result->ssl_avs_response);
                $payment->setAdditionalInformation('cvd_result', (string)$result->ssl_cvv2_response);
                $payment->setAdditionalInformation('payment_status', 'Authorize Only');
            } else {
                throw new LocalizedException(__('There is an error in payment processing, please try again.'));
            }
        }
        return $this;
    }
 
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($amount <= 0) {
            throw new LocalizedException(__('Invalid amount for capture.'));
        }
        $order = $payment->getOrder();
        if ($payment->getTransactionId() != '') {
            $ref_id = $payment->getTransactionId();
        } else {
            $ref_id = 0;    
        }
        $trn_type = 'c';
        $result = $this->createCharge($payment, $amount, $trn_type, $ref_id);
        if ($result->ssl_result != '0') {
            if (isset($result->ssl_result_message)) {
                $errorMessage = $result->ssl_result_message;
            } else {
                $errorMessage = $result->errorMessage;
            }
            throw new LocalizedException(__('Error Processing the request. '.$errorMessage));
        } else {
            if ($result->ssl_result == '0') {
                $payment->setTransactionId($result->ssl_txn_id);
                $payment->setIsTransactionClosed(1);
                $payment->setAdditionalInformation('order_tra_id', (string)$result->ssl_txn_id);
                $payment->setAdditionalInformation('payment_status', 'Captured');
                if ($payment->getTransactionId() != '') {
                    $payment->setAdditionalInformation('last_transaction_id', (string)$result->ssl_txn_id);
                } else {
                    $payment->setAdditionalInformation('real_transaction_id', (string)$result->ssl_txn_id);
                    $payment->setAdditionalInformation('avs_res_id', (string)$result->ssl_avs_response);
                    $payment->setAdditionalInformation('cvd_result', (string)$result->ssl_cvv2_response);
                }
            } else {
                throw new LocalizedException(__('There is an error in payment processing, please try again.'));
            }
        }
        return $this;
    }
    
    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($payment->getRefundTransactionId() && $amount > 0) {
            $trn_type = 'r';
            $result = $this->createCharge($payment,$amount,$trn_type,$payment->getRefundTransactionId());
            if ($result->ssl_result == '0') {
                $payment->setAdditionalInformation('last_transaction_id', (string)$result->ssl_txn_id);
                $payment->setAdditionalInformation('payment_status', 'Refunded');
            } else {
                throw new LocalizedException(__('There is an error in refund processing. '.$result->errorMessage));
            }
        } else {
            throw new LocalizedException(__('There is an error in payment processing. Please contact Elavon support.'));
        }
        return $this;
    }
    
    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        $trn_type = 'v';
        if ($payment->getTransactionId()) {
            $amount = $payment->getAmountAuthorized();
            $result = $this->createCharge($payment, null, $trn_type, $payment->getTransactionId());
            if ($result->ssl_result == '0') {
                $payment->setAdditionalInformation('last_transaction_id', (string)$result->ssl_txn_id);
                $payment->setAdditionalInformation('payment_status', 'Voided');
            } else {
                throw new LocalizedException(__('There is an error in void payment. '.$result->errorMessage));
            }
        } else {
            throw new LocalizedException(__('Error in void the payment.'));
        }
        return $this;
    }
        
    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return object
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->void($payment);
    }
    
    /**
     * Class for create charge.
    */
    private function createCharge($payment, $amount, $trn_type, $ref_id)
    {
        $order = $payment->getOrder();
        $ssl_merchant_id = $this->customHelper->getMerchantId();
        $ssl_user_id = $this->customHelper->getUserId();
        $ssl_pin = $this->customHelper->getElavonPin();
        $ssl_amount = number_format($amount, 2, '.', '');
        $ssl_salestax = number_format($order->getTaxAmount(), 2, '.', '');
        $ssl_transaction_currency = $order->getBaseCurrencyCode();
        $ssl_description = sprintf('Order # %s', $order->getIncrementId());
        $ssl_card_number = $payment->getCcNumber();
        $dt = \DateTime::createFromFormat('Y', $payment->getCcExpYear());
        $expYear = $dt->format('y');
        $expMonth = sprintf("%02d", $payment->getCcExpMonth());
        $ssl_exp_date = $expMonth.$expYear;
        $ssl_cvv2cvc2 = $payment->getCcCid(); 
        $ssl_cvv2cvc2_indicator = '1';
        if ($trn_type == 'a') {
            $ssl_transaction_type = 'ccauthonly';
        } elseif ($trn_type == 'c' && $ref_id != '0') {
            $ssl_transaction_type = 'cccomplete';
        } elseif ($trn_type == 'r') {
            $ssl_transaction_type = 'ccreturn';
        } elseif ($trn_type == 'v') {
            $ssl_transaction_type = 'ccvoid';
        } else {
            $ssl_transaction_type = 'ccsale';
        }
        
        //  *** Credit Card Billing information ***
        $billingaddress = $order->getBillingAddress();
        $ssl_first_name = $billingaddress->getData('firstname');
        if (strlen($ssl_first_name) >= 20) {
            $ssl_first_name = substr($ssl_first_name, 0, 20);
        }
        $ssl_last_name= $billingaddress->getData('lastname');
        if (strlen($ssl_last_name) >= 30) {
            $ssl_last_name = substr($ssl_last_name, 0, 30);
        }
        $ssl_company = $billingaddress->getData('company');
        if (strlen($ssl_company) >= 50) {
            $ssl_company = substr($ssl_company, 0, 50);
        }
        $ssl_avs_address = $billingaddress->getData('street');
        if (strlen($ssl_avs_address) >= 50) {
            $ssl_avs_address = substr($ssl_avs_address, 0, 50);
        }
        $ssl_city = $billingaddress->getData('city');
        if (strlen($ssl_city) >= 20) {
            $ssl_city = substr($ssl_city, 0, 20);
        }
        if ($billingaddress->getData('region_id') != '') {
            $ssl_state = $this->customHelper->getRegionCode($billingaddress->getData('region_id'));
        } else {
            $ssl_state = substr($billingaddress->getData('region'), 0, 2);
        }
        $ssl_country_id = $billingaddress->getData('country_id');
        $ssl_country = $this->customHelper->getCountryName($billingaddress->getData('country_id'));
        $ssl_avs_zip = '0';
        if (null !== $billingaddress->getData('postcode')) {
            if ($billingaddress->getData('postcode') != '') {
                $ssl_avs_zip = $billingaddress->getData('postcode');
                
                if (strlen($ssl_avs_zip) >= 9) {
                    $ssl_avs_zip = substr($ssl_avs_zip,0,9);
                }
            }
        }
        $ssl_phone = $billingaddress->getData('telephone');
        if (strlen($ssl_phone) >= 20) {
            $ssl_phone = substr($ssl_phone,0,9);
        }
        $ssl_email = $billingaddress->getData('email');
        
        $allowCvc = $this->customHelper->getConfig('payment/rootways_elavon_option/cvc_varification');
        $allowAvs = $this->customHelper->getConfig('payment/rootways_elavon_option/zipcode_varification');
        $allowBilling = $this->customHelper->getConfig('payment/rootways_elavon_option/send_billing_address');
        
        if (empty($billingaddress)) {
            throw new LocalizedException(__('Invalid billing data.'));
        }
		
        if ($trn_type != 'a' && $ref_id != '0' && $trn_type != 'v') {
            $charge_id = str_replace('-capture','',$ref_id);
            $xml = "<txn>";
            $xml .= "<ssl_merchant_id>" . $ssl_merchant_id . "</ssl_merchant_id>
                    <ssl_pin>" . $ssl_pin . "</ssl_pin>
                    <ssl_user_id>" . $ssl_user_id . "</ssl_user_id>
                    <ssl_transaction_type>".$ssl_transaction_type."</ssl_transaction_type>
                    <ssl_description>".$ssl_description."</ssl_description>
                    <ssl_txn_id>".$charge_id."</ssl_txn_id>
                    <ssl_amount>" . $ssl_amount . "</ssl_amount>";
                if ($trn_type== 'c' && $amount < $order->getGrandTotal()) {
                    $xml .= "<ssl_partial_shipment_flag>Y</ssl_partial_shipment_flag>";
                }
            $xml .= '</txn>';
        } elseif ($trn_type == 'v') {
            $void_id = str_replace('-void','',$ref_id);
            $xml = "<txn><ssl_merchant_id>" . $ssl_merchant_id . "</ssl_merchant_id>
                    <ssl_pin>" . $ssl_pin . "</ssl_pin>
                    <ssl_user_id>" . $ssl_user_id . "</ssl_user_id>
                    <ssl_transaction_type>".$ssl_transaction_type."</ssl_transaction_type>
                    <ssl_txn_id>".$void_id."</ssl_txn_id>
                </txn>";
        } else {
            $xml = "<txn>";
            $xml .= "<ssl_merchant_id>" . $ssl_merchant_id . "</ssl_merchant_id>
                <ssl_pin>" . $ssl_pin . "</ssl_pin>
                <ssl_user_id>" . $ssl_user_id . "</ssl_user_id>
                <ssl_transaction_type>" . $ssl_transaction_type . "</ssl_transaction_type>
                <ssl_card_number>" . $ssl_card_number . "</ssl_card_number>
                <ssl_exp_date>" . $ssl_exp_date . "</ssl_exp_date>
                <ssl_amount>" . $ssl_amount . "</ssl_amount>
                <ssl_salestax>" . $ssl_salestax . "</ssl_salestax>
                <ssl_invoice_number>" . $order->getIncrementId() . "</ssl_invoice_number>
                <ssl_cardholder_ip>".$this->getClientIp()."</ssl_cardholder_ip>
                <ssl_description>" . $ssl_description . "</ssl_description>
                <products>" . $ssl_amount . "::1::001::" . $ssl_description . "::</products>
                <ssl_email>" . $ssl_email . "</ssl_email>
                ";
            
            if ($allowBilling) {
                $xml .= "<ssl_first_name>" . $ssl_first_name . "</ssl_first_name>
                    <ssl_last_name>" . $ssl_last_name . "</ssl_last_name>
                    <ssl_company>" . $ssl_company . "</ssl_company>
                    <ssl_city>" . $ssl_city . "</ssl_city>
                    <ssl_state>" . $ssl_state . "</ssl_state>
                    <ssl_country>" . $ssl_country_id . "</ssl_country>
                    <ssl_phone>" . $ssl_phone . "</ssl_phone>";
            }
            
            if ($allowAvs) {
               $xml .= "<ssl_avs_address>" . substr($ssl_avs_address,0,25) . "</ssl_avs_address>
                    <ssl_avs_zip>" . $ssl_avs_zip . "</ssl_avs_zip>";
            }
            
            if ($allowCvc) {
                $xml .= "<ssl_cvv2cvc2_indicator>" . $ssl_cvv2cvc2_indicator . "</ssl_cvv2cvc2_indicator>
                <ssl_cvv2cvc2>" . $ssl_cvv2cvc2 . "</ssl_cvv2cvc2>";
            }
            
            if ($this->customHelper->getConfig('payment/rootways_elavon_option/multi_currency') == 1) {
                $xml .= "<ssl_transaction_currency>" . $ssl_transaction_currency . "</ssl_transaction_currency>";
            }
            $xml .= '</txn>';
        }
        //$xmlTemp = simplexml_load_string($xml); file_put_contents('vish_elavon_request.txt', print_r($xmlTemp, true), FILE_APPEND);
        $postURL = $this->customHelper->getPostUrl();
        $replaceFrom = ["%", "=", "&"];
        $replaceTo = ["percentage", "equal", "and"];
        $xmlNew = str_replace($replaceFrom, $replaceTo, $xml);
        $postData = "xmldata=" . URLEncode($xmlNew);
        $session = curl_init();
        $header[] = "Content-Length: " . strlen($postData);
        $header[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($session, CURLOPT_URL, $postURL);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_HTTPHEADER, $header);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($session);
        //file_put_contents('vish_elavon_response.txt', print_r($response, true), FILE_APPEND);
        $result = simplexml_load_string($response);
        
        if ($this->customHelper->isDebugEnabled()) {
            $reqOut = $this->delete_all_between('<ssl_merchant_id>', '</ssl_exp_date>', $xml);
            $resOut = $this->delete_all_between('<ssl_txn_id>', '</ssl_account_balance>', $response);
            $this->rwLogger($reqOut, $resOut);
        }
        return $result;
    }
    
    /**
     * @param \Magento\Framework\DataObject $data
    */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        return $this;
    }
    
    public function getClientIp() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
    function delete_all_between($beginning, $end, $string) {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }
        
        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
        
        return $this->delete_all_between($beginning, $end, str_replace($textToDelete, '', $string)); // recursion to ensure all occurrences are replaced
    }
    
    public function rwLogger($req, $res) {
        $logger = new \Zend\Log\Logger();
        $rwLog = new \Zend\Log\Writer\Stream(BP.'/var/log/rw_elavon.log');
        $logger->addWriter($rwLog);
        $logger->info("#######Request#######");
        $logger->info($req);
        $logger->info("#######Response#######");
        $logger->info($res);
    }
}
