<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Ui;

use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class RedirectConfigProvider
 */
class RedirectConfigProvider implements ConfigProviderInterface
{
    const METHOD_CODE = 'elavon';
    const TRANSACTION_DATA_URL = 'elavon/redirect/gettransactiondata';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ConfigProvider constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::METHOD_CODE => [
                    'transactionDataUrl' => $this->urlBuilder->getUrl(self::TRANSACTION_DATA_URL, ['_secure' => true]),
                ]
            ]
        ];
    }
}
