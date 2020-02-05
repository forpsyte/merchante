/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Merchantesolutions/js/model/tokenize'
], function (tokenizationService) {
    'use strict';

    /**
     * Validate the credit card number
     * @param value
     * @return {boolean}
     */
    function isValid(value) {

        // Accept only digits, dashes or spaces
        if (/[^0-9-\s]+/.test(value)) return false;

        let nCheck = 0, bEven = false;
        value = value.replace(/\D/g, "");

        for (var n = value.length - 1; n >= 0; n--) {
            var cDigit = value.charAt(n),
                nDigit = parseInt(cDigit, 10);

            if (bEven && (nDigit *= 2) > 9) nDigit -= 9;

            nCheck += nDigit;
            bEven = !bEven;
        }

        return (nCheck % 10) === 0;
    }

    return function(ccNumber, ccExpMonth, ccExpYear){
        let serviceUrl, payload, method = 'merchantesolutions';

        payload = {
            transaction_type: 'T',
            card_number: ccNumber,
            card_exp_date: ccExpMonth + ccExpYear,
            resp_encoding: 'json',
        };

        serviceUrl = window.checkoutConfig.payment[method].environmentUrl;

        return tokenizationService(serviceUrl, payload);
    };
});
