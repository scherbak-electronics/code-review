/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Magento_Ui/js/dynamic-rows/action-delete'
], function (ActionDelete) {
    'use strict';

    return ActionDelete.extend({
        defaults: {
            downloadLabel: 'Download',
            downloadFileName: '',
            downloadBaseUrl: '',
            links: {
                downloadFileName: '${ $.provider }:${ $.dataScope }.${ $.downloadFileNameIndex }'
            }
        },

        /**
         *
         * @returns {string}
         */
        getDownloadLink: function () {
            if (this.downloadFileName) {
                return this.downloadBaseUrl + this.downloadFileName;
            }

            return '';
        }
    });
});
