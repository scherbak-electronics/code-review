<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class RemoteConfigProvider
 */
class RemoteConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * ConfigProvider constructor.
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                ConfigInterface::METHOD_CODE => [
                    'hasSsIssueNumber' => $this->config->getIsSsIssueNumber(),
                    'ssStartYears' => $this->config->getSsStartYears()
                ]
            ]
        ];
    }
}
