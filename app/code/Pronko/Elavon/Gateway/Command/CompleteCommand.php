<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;

/**
 * Class CompleteCommand
 */
class CompleteCommand implements CommandInterface
{
    /**
     * @var CommandInterface
     */
    private $updateDetailsCommand;

    /**
     * @var CommandInterface
     */
    private $updateOrderCommand;

    /**
     * CompleteCommand constructor.
     * @param CommandInterface $updateDetailsCommand
     * @param CommandInterface $updateOrderCommand
     */
    public function __construct(
        CommandInterface $updateDetailsCommand,
        CommandInterface $updateOrderCommand
    ) {
        $this->updateDetailsCommand = $updateDetailsCommand;
        $this->updateOrderCommand = $updateOrderCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $this->updateDetailsCommand->execute($commandSubject);
        $this->updateOrderCommand->execute($commandSubject);
    }
}
