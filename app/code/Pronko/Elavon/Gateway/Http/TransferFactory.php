<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Http\Converter\ArrayToXml;

/**
 * Class TransferFactory
 * @package     Pronko\Elavon\Gateway\Http
 */
class TransferFactory implements TransferFactoryInterface
{
    /**
     * Request Timeout
     */
    const REQUEST_TIMEOUT = 30;
    /**
     * Request Method
     */
    const REQUEST_METHOD = 'POST';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @var ArrayToXml
     */
    private $arrayToXml;

    /**
     * TransferFactory constructor.
     * @param ConfigInterface $config
     * @param TransferBuilder $transferBuilder
     * @param ArrayToXml $arrayToXml
     */
    public function __construct(
        ConfigInterface $config,
        TransferBuilder $transferBuilder,
        ArrayToXml $arrayToXml
    ) {
        $this->config = $config;
        $this->transferBuilder = $transferBuilder;
        $this->arrayToXml = $arrayToXml;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        $body = $this->arrayToXml->convert($request)->saveXML();
        return $this->transferBuilder
            ->setClientConfig([
                'timeout' => self::REQUEST_TIMEOUT,
                'verifypeer' => true
            ])
            ->setMethod(self::REQUEST_METHOD)
            ->setBody($body)
            ->setUri($this->config->getGatewayUrl())
            ->build();
    }
}
