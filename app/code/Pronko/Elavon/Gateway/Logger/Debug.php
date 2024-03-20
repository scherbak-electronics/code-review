<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Logger;

use Magento\Framework\Logger\Handler\Debug as DebugLogger;

/**
 * Class Debug
 */
class Debug extends DebugLogger
{
    const DEFAULT_DEBUG_FILE_NAME = '/var/log/debug_elavon.log';

    /**
     * @var string
     */
    protected $fileName = self::DEFAULT_DEBUG_FILE_NAME; // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
}
