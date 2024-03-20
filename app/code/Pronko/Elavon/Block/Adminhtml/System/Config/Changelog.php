<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template;
use Pronko\Elavon\Source\ChangelogProvider;

/**
 * Class Changelog
 */
class Changelog extends Template
{
    /**
     * @var ChangelogProvider
     */
    private $changelogProvider;

    /**
     * Changelog constructor.
     * @param Template\Context $context
     * @param ChangelogProvider $changelogProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ChangelogProvider $changelogProvider,
        array $data = []
    ) {
        $this->changelogProvider = $changelogProvider;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getChangelogJson()
    {
        return json_encode([
            'changelog' => $this->getContent()
        ]);
    }

    /**
     * @return string
     */
    private function getContent()
    {
        $content = $this->changelogProvider->get();
        $content = str_replace(["\r\n", "\n"], "\n", $content);

        $result = '';
        $open = false;
        foreach (explode("\n", $content) as $line) {
            if (stripos($line, '*') !== false) {
                if (!$open) {
                    $open = true;
                    $result .= '<ul class="elavon-changelog-list">';
                }
            } elseif ($open) {
                $open = false;
                $result .= '</ul>';
            }

            if (stripos($line, '###') !== false) {
                $result .= '<h2>' . str_replace('###', '', $line) . '</h2>';
            } elseif (stripos($line, '*') !== false) {
                $result .= '<li>' . str_replace('*', '', $line) . '</li>';
            } else {
                $result .= $line;
            }
        }

        return '<span class="">' . $result . '</span>';
    }
}
