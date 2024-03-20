<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Redirect;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class RequestBuilder
 */
class OrderDataBuilder implements BuilderInterface
{
    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * OrderDataBuilder constructor.
     * @param BuilderInterface $builder
     * @param ConfigInterface $config
     */
    public function __construct(
        BuilderInterface $builder,
        ConfigInterface $config
    ) {
        $this->builder = $builder;
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(array $buildSubject)
    {
        $fields = $this->builder->build($buildSubject);

        return [
            'action' => $this->config->getRedirectUrl(),
            'fields' => array_keys(array_change_key_case($fields, CASE_UPPER)),
            'values' => array_values($fields)
        ];
    }
}
