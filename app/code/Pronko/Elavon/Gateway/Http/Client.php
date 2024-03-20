<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Http;

use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface as CriticalLogger;

/**
 * Class Client
 */
class Client implements ClientInterface
{
    /**
     * @var ZendClientFactory
     */
    private $clientFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var CriticalLogger
     */
    private $criticalLogger;

    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * Client constructor.
     * @param ZendClientFactory $clientFactory
     * @param Logger $logger
     * @param CriticalLogger $criticalLogger
     * @param ConverterInterface $converter
     */
    public function __construct(
        ZendClientFactory $clientFactory,
        Logger $logger,
        CriticalLogger $criticalLogger,
        ConverterInterface $converter
    ) {
        $this->logger = $logger;
        $this->criticalLogger = $criticalLogger;
        $this->converter = $converter;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array
     * @throws ClientException
     * @throws ConverterException
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $log = [
            'request_uri' => $transferObject->getUri(),
            'request' => $this->converter->convert($transferObject->getBody())
        ];
        $result = [];

        try {
            /** @var ZendClient $client */
            $client = $this->clientFactory->create();
            $client->setConfig($transferObject->getClientConfig());
            $client->setMethod($transferObject->getMethod());
            $client->setRawData($transferObject->getBody(), 'text/xml');
            $client->setHeaders($transferObject->getHeaders());
            $client->setUrlEncodeBody($transferObject->shouldEncode());
            $client->setUri($transferObject->getUri());

            $response = $client->request();

            $result = $this->converter->convert($response->getBody());
            $log['response'] = $result;
        } catch (\Zend_Http_Client_Exception $e) {
            $this->criticalLogger->critical($e);
            throw new ClientException(__('Unable to place transaction. Please try again later.'));
        } catch (ConverterException $e) {
            throw $e;
        } finally {
            $this->logger->debug($log);
        }

        return $result;
    }
}
