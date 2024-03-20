<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\DownloadsImportExport\Ui\Component;


/**
 * Class ExportButton
 */
class ExportButton extends \Magento\Ui\Component\ExportButton
{
    /**
     * @return void
     */
    public function prepare()
    {
        parent::prepare();

        $config = $this->getData('config');

        if (isset($config['options'])) {
            $options = [];

            foreach ($config['options'] as $option) {

                // Only CSV format is possible
                if ($option['value'] !== 'csv') {
                    continue;
                }

                $options[$option['value']] = $option;
            }
            $config['options'] = array_values($options);
            $this->setData('config', $config);
        }
    }
}
