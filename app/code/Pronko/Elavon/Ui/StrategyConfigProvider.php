<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Pronko\Elavon\Spi\ConfigInterface;

class StrategyConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ConfigProviderInterface[]
     */
    private $configProviders;

    /**
     * StrategyConfigProvider constructor.
     * @param ConfigInterface $config
     * @param array $configProviders
     */
    public function __construct(
        ConfigInterface $config,
        array $configProviders
    ) {
        $this->config = $config;
        $this->configProviders = $configProviders;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $type = $this->config->getConnectionType();

        $config = [];
        if (isset($this->configProviders[$type])) {
            $config = $this->configProviders[$type]->getConfig();
            $config['payment'][ConfigInterface::METHOD_CODE]['connectionType'] = $type;
        }
        return $config;
    }
}
