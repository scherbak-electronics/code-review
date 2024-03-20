<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin;

use Cart2Quote\Not2Order\Helper\Data;
use Cart2Quote\Not2Order\Html\Parser;

/**
 * Class BasePlugin
 * @package Cart2Quote\Not2Order\Plugin
 */
abstract class BasePlugin
{
    /**
     * @var Data
     */
    protected $dataHelper;
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * BasePlugin constructor.
     * @param \Cart2Quote\Not2Order\Html\Parser $parser
     * @param \Cart2Quote\Not2Order\Helper\Data $dataHelper
     */
    public function __construct(Parser $parser, Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
        $this->parser = $parser;
    }
}
