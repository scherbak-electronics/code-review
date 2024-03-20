<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request;

use Magento\Framework\App\ProductMetadataInterface;
use Pronko\Elavon\Spi\ConfigInterface;

class Version
{
    /**
     * Extension version
     */
    const EXTENSION_VERSION = 'Pronko Elavon v%s';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Version constructor.
     * @param ConfigInterface $config
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ConfigInterface $config,
        ProductMetadataInterface $productMetadata
    ) {
        $this->config = $config;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        $version = $this->config->getModuleVersion();
        return sprintf(self::EXTENSION_VERSION, $version);
    }

    /**
     * @return string
     */
    public function getProductVersion()
    {
        return sprintf(
            __('%s %s ver.%s'),
            $this->productMetadata->getName(),
            $this->productMetadata->getEdition(),
            $this->productMetadata->getVersion()
        );
    }
}
