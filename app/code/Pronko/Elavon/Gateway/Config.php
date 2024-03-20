<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway;

use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Module\ModuleListInterface;
use Pronko\Elavon\Gateway\Config\CommonConfig;
use Pronko\Elavon\Source\Environment;
use Pronko\Elavon\Source\PaymentAction;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Pronko\Elavon\Spi\ConfigInterface as SpiConfigInterface;

/**
 * Class Config
 * @private
 */
class Config extends CommonConfig implements SpiConfigInterface
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ModuleListInterface $moduleList
     * @param DecoderInterface $decoder
     * @param string $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ModuleListInterface $moduleList,
        DecoderInterface $decoder,
        $methodCode,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        $this->moduleList = $moduleList;
        $this->decoder = $decoder;

        parent::__construct($scopeConfig, $methodCode, $pathPattern);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getGatewayUrl($storeId = null)
    {
        return $this->getEnvironment() === Environment::PRODUCTION ?
            $this->getValue(SpiConfigInterface::GATEWAY_URL, $storeId) :
            $this->getValue(SpiConfigInterface::GATEWAY_URL_SANDBOX, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getMerchantId($storeId = null)
    {
        return $this->getValue(SpiConfigInterface::MERCHANT_ID, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getAccount($storeId = null)
    {
        return $this->getValue(SpiConfigInterface::ACCOUNT, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getRefundPassword($storeId = null)
    {
        return $this->getValue(SpiConfigInterface::REFUND_PASSWORD, $storeId);
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        $moduleInfo = $this->moduleList->getOne(SpiConfigInterface::MODULE_NAME);
        return isset($moduleInfo['setup_version']) ? $moduleInfo['setup_version'] : '1.0.0';
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getOrderPrefix($storeId = null)
    {
        return $this->getValue(SpiConfigInterface::ORDER_PREFIX, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getIsSsIssueNumber($storeId = null)
    {
        return (bool)$this->getValue(SpiConfigInterface::SS_ISSUE_NUMBER, $storeId);
    }

    /**
     * @return array
     */
    public function getSsStartYears()
    {
        $years = [];
        $first = date("Y");

        for ($index = 5; $index >= 0; $index--) {
            $year = $first - $index;
            $years[$year] = $year;
        }
        return $years;
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getSecret($storeId = null)
    {
        return $this->getValue(SpiConfigInterface::SECRET, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getUseCvv($storeId = null)
    {
        return $this->getValue(SpiConfigInterface::USE_CVV, $storeId);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getSubAccount($type)
    {
        $subAccounts = $this->decoder->decode($this->getValue(SpiConfigInterface::SUB_ACCOUNTS));

        $account = null;
        if (is_array($subAccounts)) {
            foreach ($subAccounts as $subAccount) {
                if (isset($subAccount['card_types']) && false !== in_array($type, $subAccount['card_types'])) {
                    $account = $subAccount['name'];
                    break;
                }
            }
        }

        if (empty($account)) {
            return $this->getValue('subaccount');
        }

        return $account;
    }

    /**
     * @return bool
     */
    public function canCapturePartial()
    {
        return (bool)$this->getValue(SpiConfigInterface::CAN_CAPTURE_PARTIAL);
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->getValue(SpiConfigInterface::ENVIRONMENT);
    }

    /**
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->getValue(SpiConfigInterface::PAYMENT_ACTION);
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getEnvironment() === Environment::PRODUCTION ?
            $this->getValue('redirect_url') :
            $this->getValue('redirect_url_sandbox');
    }

    /**
     * Returns autosettle flag
     *
     * 0 - authorize
     * 1 - capture
     *
     * @return int
     */
    public function getAutoSettle()
    {
        return (int)($this->getPaymentAction() === PaymentAction::CAPTURE);
    }

    /**
     * @return string
     */
    public function getConnectionType()
    {
        return $this->getValue(SpiConfigInterface::CONNECTION_TYPE);
    }
}
