/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/storage'
], function ($, storage) {
    'use strict';

    return function(serviceUrl, payload){
        let method = 'merchantesolutions';

        return $.ajax({
            url: serviceUrl,
            type: 'POST',
            data: payload,
            contentType: 'application/x-www-form-urlencoded',
            xhrFields: {
                withCredentials: false
            }
        });
    };
});
