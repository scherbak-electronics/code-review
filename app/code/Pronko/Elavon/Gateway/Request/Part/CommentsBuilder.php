<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Part;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Gateway\Request\Version;

/**
 * Class CommentsBuilder
 */
class CommentsBuilder implements BuilderInterface
{
    /**#@+
     * Request node names constants
     */
    const COMMENTS = 'comments';
    const COMMENT = 'comment';
    const ID = 'id';
    /**#@-*/

    /**
     * Max Comment length
     */
    const MAX_COMMENT_LENGTH = 50;

    /**
     * Extension version
     */
    const EXTENSION_VERSION = 'Pronko Elavon v%s';

    /**
     * @var Version
     */
    private $version;

    /**
     * CommentsBuilder constructor.
     * @param Version $version
     */
    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    // @codingStandardsIgnoreStart
    public function build(array $buildSubject)
    {// @codingStandardsIgnoreEnd
        return [
            self::COMMENTS => [
                'comment1' => [
                    '_name'=> self::COMMENT,
                    '_attribute' => [
                        self::ID => 1
                    ],
                    '_value' => $this->version->getProductVersion(),
                ],
                'comment2' => [
                    '_name'=> self::COMMENT,
                    '_attribute' => [
                        self::ID => 2
                    ],
                    '_value' => $this->version->getVersion(),
                ]
            ]
        ];
    }
}
