/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'underscore',
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Vault/js/view/payment/vault-enabler',
        'Magento_Ui/js/model/messageList',
        'Merchante_Merchante/js/action/tokenize',
    ],
    function (
        _,
        $,
        Component,
        VaultEnabler,
        globalMessageList,
        tokenizeAction,
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Merchante_Merchante/payment/cc-form',
                active: false,
                code: 'merchante',
                lastBillingAddress: null,
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardSsIssue: '',
                creditCardVerificationNumber: '',
                selectedCardType: null,
                creditCardToken: null,
                additionalData: {}
            },

            /**
             * @returns {exports.initialize}
             */
            initialize: function() {
                let self = this;
                this._super();
                this.vaultEnabler = new VaultEnabler();
                this.vaultEnabler.setPaymentCode(this.getVaultCode());
                return self;
            },

            /** @inheritdoc */
            initObservable: function () {
                this._super()
                    .observe([
                        'active',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'creditCardSsIssue',
                        'selectedCardType',
                        'creditCardToken'
                    ]);

                return this;
            },

            /**
             * Initialize event observers
             *
             * @returns void
             */
            initEventObservers: function() {
                // Format the credit card number field
                let ccNumberSelector = this.getSelector('cc_number');
                $(ccNumberSelector).on('keypress change', function () {
                    $(this).val(function (index, value) {
                        return value.replace(/[^0-9]/g, "").replace(/\W/gi, '').replace(/(.{4})/g, '$1 ');
                    });
                });
            },

            /**
             * Get code
             *
             * @returns {String}
             */
            getCode: function () {
                return this.code;
            },

            /**
             * Get data
             *
             * @returns {Object}
             */
            getData: function() {
                let data = {
                    'method': this.getCode(),
                    'additional_data': {
                        'card_id': this.creditCardToken(),
                        'cc_type': this.selectedCardType(),
                        'cc_number': 'XXXXXXXXXXXX' + this.creditCardNumber().replace(/\s/g,'').substr(-4),
                        'store_card': this.vaultEnabler.isActivePaymentTokenEnabler(),
                        'cvv2': this.creditCardVerificationNumber()
                    },
                };

                let parentData = this._super();

                data['additional_data'] =  _.extend(parentData['additional_data'], data['additional_data']);
                this.vaultEnabler.visitAdditionalData(data);

                return data;
            },

            /**
             * Check if payment is active
             *
             * @returns {Boolean}
             */
            isActive: function () {
                let active = this.getCode() === this.isChecked();

                this.active(active);

                return active;
            },

            /**
             * Returns state of place order button
             *
             * @returns {Boolean}
             */
            isButtonActive: function () {
                return this.isActive() && this.isPlaceOrderActionAllowed();
            },

            validate: function() {
              let formSelector = this.getSelector('cc_form'),
                  form = $(formSelector);
              return form.validation() && form.validation('isValid');
            },

            /**
             * Place order.
             *
             * x@returns void
             */
            beforePlaceOrder: function (data, event) {
                if(this.validate()) {
                    let self = this,
                        ccNumber = this.creditCardNumber().replace(/\s/g,''),
                        ccExpMonth = this.creditCardExpMonth() < 10 ? '0' +
                            this.creditCardExpMonth() : this.creditCardExpMonth(),
                        ccExpYear = this.creditCardExpYear().slice(2);

                    if (event) {
                        event.preventDefault();
                    }

                    this.tokenize(ccNumber, ccExpMonth, ccExpYear)
                        .done(function(response){
                            if (response['error_code'] !== '000') {
                               self.showError(response['auth_response_text']);
                               return;
                            }
                            self.creditCardToken(response['transaction_id']);
                            self.placeOrder(data, event)
                        });
                }
            },

            /**
             * Tokenize the credit card number.
             *
             * @param {String} ccNumber
             * @param {String} ccExpMonth
             * @param {String} ccExpYear
             * @return {jQuery}
             */
            tokenize: function(ccNumber, ccExpMonth, ccExpYear) {
                return $.when(tokenizeAction(ccNumber, ccExpMonth, ccExpYear));
            },

            /**
             * Get full selector name
             *
             * @param {String} field
             * @returns {String}
             * @private
             */
            getSelector: function (field) {
                return '#' + this.getCode() + '_' + field;
            },

            /**
             * @returns {Boolean}
             */
            isVaultEnabled: function () {
                return this.vaultEnabler.isVaultEnabled();
            },

            /**
             * Returns vault code.
             *
             * @returns {String}
             */
            getVaultCode: function () {
                return window.checkoutConfig.payment[this.getCode()].ccVaultCode;
            },

            /**
             * Show error message
             *
             * @param {String} errorMessage
             * @private
             */
            showError: function (errorMessage) {
                globalMessageList.addErrorMessage({
                    message: errorMessage
                });
            }
        });
    }
);
