<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Command\Result\ArrayResultFactory;
use Pronko\Elavon\Gateway\Request\Redirect\OrderDataBuilder;

class RedirectCommand implements CommandInterface
{
    /**
     * @var OrderDataBuilder
     */
    private $builder;

    /**
     * @var ArrayResultFactory
     */
    private $arrayResultFactory;

    /**
     * @param OrderDataBuilder $builder
     * @param ArrayResultFactory $arrayResultFactory
     */
    public function __construct(
        OrderDataBuilder $builder,
        ArrayResultFactory $arrayResultFactory
    ) {
        $this->builder = $builder;
        $this->arrayResultFactory = $arrayResultFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $result = $this->builder->build($commandSubject);

        return $this->arrayResultFactory->create(['array' => $result]);
    }
}
