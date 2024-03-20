<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Test\Unit\Gateway\Http;

use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\TestCase;
use Pronko\Elavon\Gateway\Http\Client;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Model\Method\Logger;

/**
 * Class ClientTest
 */
class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ZendClientFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $clientFactory;

    /**
     * @var ConverterInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $converter;

    /**
     * @var Logger | \PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    /**
     * @var TransferInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $transferObject;

    /**
     * @var ZendClient | \PHPUnit_Framework_MockObject_MockObject
     */
    private $httpClient;

    protected function setUp() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->clientFactory = $this->createMock(ZendClientFactory::class);
        $this->converter = $this->getMockBuilder(ConverterInterface::class)
            ->setMethods(['convert'])->getMock();
        $this->logger = $this->createMock(Logger::class);
        $this->transferObject = $this->createMock(TransferInterface::class);
        $this->httpClient = $this->createMock(ZendClient::class);

        $this->clientFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->httpClient);

        $objectManager = new ObjectManager($this);
        $this->client = $objectManager->getObject(
            Client::class,
            [
                'clientFactory' => $this->clientFactory,
                'converter' => $this->converter,
                'logger' => $this->logger
            ]
        );
    }

    public function testPlaceRequest()
    {
        $body = '<request type="auth"></request>';
        $responseBody = '<xml><node>test</node></xml>';
        $uri = 'test/uri';
        $clientConfig = ['config'];
        $headers = ['headers'];
        $shouldEncode = 1;

        $response = $this->getMockBuilder(ResponseFixture::class)
            ->setMethods(['getBody'])->getMock();
        $response->expects($this->once())->method('getBody')->willReturn($responseBody);

        /** Configure transfer object */
        $this->transferObject->expects($this->any())->method('getBody')->willReturn($body);
        $this->transferObject->expects($this->any())->method('getUri')->willReturn($uri);
        $this->transferObject->expects($this->once())->method('getClientConfig')->willReturn($clientConfig);
        $this->transferObject->expects($this->once())->method('getHeaders')->willReturn($headers);
        $this->transferObject->expects($this->once())->method('shouldEncode')->willReturn($shouldEncode);

        /** Client expectations */
        $this->httpClient->expects($this->once())->method('request')->willReturn($response);
        $this->httpClient->expects($this->once())->method('setConfig')->with($clientConfig);
        $this->httpClient->expects($this->once())->method('setRawData')->with($body);
        $this->httpClient->expects($this->once())->method('setHeaders')->with($headers);
        $this->httpClient->expects($this->once())->method('setUrlEncodeBody')->with($shouldEncode);
        $this->httpClient->expects($this->once())->method('setUri')->with($uri);

        $this->converter->expects($this->any())->method('convert')->willReturn($responseBody);

        /** @var TransferInterface $transferObject */
        $this->assertEquals($responseBody, $this->client->placeRequest($this->transferObject));
    }
}
