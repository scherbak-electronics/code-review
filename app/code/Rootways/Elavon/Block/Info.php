<?php
/**
 * Elavon Module Info Block.
 *
 * @category  Payment Integration
 * @package   Rootways_Elavon
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/shop/media/extension_doc/license_agreement.pdf
 */
namespace Rootways\Elavon\Block;

class Info extends \Magento\Payment\Block\Info
{
    protected $_isCheckoutProgressBlockFlag = true;
    
    /**
     * @var \Rootways\Elavon\Helper\Data
     */
    protected $customHelper;
    
    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;
    
    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction
     */
    protected $paymentModel;
    
    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;
    
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $currencyHelper;
    
    protected $_avsCode = array (
        'A' => 'Address matches - ZIP Code does not match', 'B' => 'Street address match, Postal code in wrong format (international issuer)',
        'C' => 'Street address and postal code in wrong formats', 'D' => 'Street address and postal code match (international issuer)',
        'E' => 'AVS Error', 'F' => 'Address does compare and five-digit ZIP code does compare (UK only)',
        'G' => 'Service not supported by non-US issuer', 'I' => 'Address information not verified by international issuer',
        'M' => 'Street Address and Postal code match (international issuer)', 'N' => 'No Match on Address (Street) or ZIP',
        'O' => 'No Response sent', 'P' => 'Postal codes match, Street address not verified due to incompatible formats',
        'R' => 'Retry, System unavailable or Timed out', 'S' => 'Service not supported by issuer',
        'U' => 'Address information is unavailable', 'W' => '9-digit ZIP matches, Address (Street) does not match',
        'X' => 'Exact AVS Match', 'Y' => 'Address (Street) and 5-digit ZIP match','Z' => '5-digit ZIP matches, Address (Street) does not match'
    );
    
    protected $_cvvCode = array (
        'M' => 'CVV2/CVC2 Match', 'N' => 'CVV2/CVC2 No match', 'P' => 'Not processed',
        'S' => 'Issuer indicates that the CVV2/CVC2 data should be present on the card, but the merchant has indicated that the CVV2/CVC2 data is not resent on the card',
        'U' => 'Issuer has not certified for CVV2/CVC2 or Issuer has not provided Visa with the CVV2/CVC2 encryption keys'
    );
    
    /**
     * Info block.
     * @param Magento\Framework\View\Element\Template\Context $context
     * @param Magento\Payment\Model\Config $paymentConfig
     * @param Magento\Store\Model\StoreManager $storeManager
     * @param Magento\Sales\Model\Order\Payment\Transaction $payment
     * @param Rootways\Elavon\Helper\Data $customHelper
     * @param Magento\Framework\Pricing\Helper\Data $currencyHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Sales\Model\Order\Payment\Transaction $payment,
        \Rootways\Elavon\Helper\Data $customHelper,
        \Magento\Framework\Pricing\Helper\Data $currencyHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
        $this->_paymentConfig = $paymentConfig;
        $this->paymentModel = $payment;
        $this->currencyHelper = $currencyHelper;
        $this->customHelper = $customHelper;
    }

    public function setCheckoutProgressBlock($flag)
    {
        $this->_isCheckoutProgressBlockFlag = $flag;
        return $this;
    }
    
    public function getCcTypeName()
    {
        $types = $this->_paymentConfig->getCcTypes();
        $ccType = $this->getInfo()->getCcType();
        if (isset($types[$ccType])) {
            return $types[$ccType];
        }
        return empty($ccType) ? __('N/A') : $ccType;
    }
    
    public function hasCcExpDate()
    {
        return (int) $this->getInfo()->getCcExpMonth() || (int) $this->getInfo()->getCcExpYear();
    }
    
    public function getCcExpMonth()
    {
        $month = $this->getInfo()->getCcExpMonth();
        if ($month < 10) {
            $month = '0'.$month;
        }
        return $month;
    }
    
    public function getCcExpDate()
    {
        $date = new \DateTime('now', new \DateTimeZone($this->_localeDate->getConfigTimezone()));
        $date->setDate($this->getInfo()->getCcExpYear(), $this->getInfo()->getCcExpMonth() + 1, 0);
        return $date;
    }
    
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        $info = $this->getInfo();
        if ($ccType = $this->getCcTypeName()) {
            $data[(string) __('Credit Card Type')] = $ccType;
        }
        if ($info->getCcLast4()) {
            $data[(string) __('Credit Card Number')] = sprintf('xxxx-%s', $info->getCcLast4());
        }
        
        if (isset($info->getAdditionalInformation()['real_transaction_id'])) {
            $data[(string) __('Transaction ID')] = $info->getAdditionalInformation()['real_transaction_id'];
        }
        if (isset($info->getAdditionalInformation()['last_transaction_id'])) {
            $data[(string) __('Last Transaction ID')] = $info->getAdditionalInformation()['last_transaction_id'];
        }
        if (isset( $info->getAdditionalInformation()['avs_res_id'])) {
            if (isset($this->_avsCode[$info->getAdditionalInformation()['avs_res_id']])) {
                $data[(string) __('AVS Response')] = $info->getAdditionalInformation()['avs_res_id'].' ('.$this->_avsCode[$info->getAdditionalInformation()['avs_res_id']].')';
            }
        }
        if (isset($info->getAdditionalInformation()['cvd_result'])) {
            if (isset($this->_cvvCode[$info->getAdditionalInformation()['cvd_result']])) {
                $data[(string) __('CVV Response')] = $info->getAdditionalInformation()['cvd_result'].' ('.$this->_cvvCode[$info->getAdditionalInformation()['cvd_result']].')';
            }
        }
        if (isset($info->getAdditionalInformation()['payment_status'])) {
            $data[(string) __('Payment Status')] = $info->getAdditionalInformation()['payment_status'];
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }
    
    protected function _formatCardDate($year, $month)
    {
        return sprintf('%s/%s', sprintf('%02d', $month), $year);
    }

    public function getSpecificInformation()
    {
        return $this->_prepareSpecificInformation()->getData();
    }
}
