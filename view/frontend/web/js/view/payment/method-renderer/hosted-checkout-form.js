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
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Vault/js/view/payment/vault-enabler',
        'Magento_Ui/js/model/messageList',
        'loader',
    ],
    function (
        _,
        $,
        Component,
        quote,
        VaultEnabler,
        globalMessageList,
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_Merchantesolutions/payment/hosted-checkout-form',
                checkoutSelector: '#checkout',
                active: false,
                code: 'merchantesolutions_hosted_checkout',
                lastBillingAddress: null,
                paymentStatusInterval: null,
                profileId: null,
                quoteId: null,
                transactionAmount: null,
                transactionType: null,
                gatewayUrl: null,
                cancelUrl: null,
                returnUrl: null,
                verificationUrl: null,
                responseUrl: null,
                cardHolderStreetAddress: null,
                cardHolderZip: null,
                cardHolderFirstName: null,
                cardHolderLastName: null,
                cardholderPhone: null,
                shipToFirstName: null,
                shipToLastName: null,
                shipToAddress: null,
                useSimulator: null,
                additionalData: {}
            },

            /**
             * @returns {exports.initialize}
             */
            initialize: function(config) {
                let self = this;
                this._super();
                this.initFields();

                quote.billingAddress.subscribe(function(address){
                    if(address !== null) {
                        self.cardHolderStreetAddress(address.street[0]);
                        self.cardHolderZip(address.postcode);
                        self.cardHolderFirstName(address.firstname);
                        self.cardHolderLastName(address.lastname);
                        self.cardHolderPhone(address.telephone);
                    }

                }, this);

                quote.shippingAddress.subscribe(function(address){
                    if(address !== null) {
                        self.shipToAddress(address.street[0]);
                        self.shipToFirstName(address.firstname);
                        self.shipToFirstName(address.lastname);
                    }
                }, this);

                return self;
            },

            /** @inheritdoc */
            initObservable: function () {
                this._super()
                    .observe([
                        'active',
                        'transactionAmount',
                        'cardHolderStreetAddress',
                        'cardHolderZip',
                        'cardHolderFirstName',
                        'cardHolderLastName',
                        'cardHolderPhone',
                        'shipToFirstName',
                        'shipToLastName',
                        'shipToAddress',
                    ]);

                return this;
            },

            /**
             * @returns void
             */
            initFields: function() {
                let config = window.checkoutConfig.payment[this.getCode()],
                    billingAddress = quote.billingAddress(),
                    shippingAddress = quote.shippingAddress();

                this.profileId = config.profileId;
                this.transactionType = config.transactionType;
                this.transactionAmount = quote.totals()['grand_total'];
                this.quoteId = quote.getQuoteId();
                this.gatewayUrl = config.gatewayUrl;
                this.cancelUrl = config.cancelUrl;
                this.returnUrl = config.returnUrl;
                this.responseUrl = config.responseUrl;
                this.verificationUrl = config.verificationUrl;
                this.spinnerAssetUrl = config.spinnerAssetUrl;
                this.useSimulator = config.useSimulator;
                this.cardHolderStreetAddress(billingAddress.street[0]);
                this.cardHolderZip(billingAddress.postcode);
                this.cardHolderFirstName(billingAddress.firstname);
                this.cardHolderLastName(billingAddress.lastname);
                this.cardHolderPhone(billingAddress.telephone);
                this.shipToAddress(shippingAddress.street[0]);
                this.shipToFirstName(shippingAddress.firstname);
                this.shipToLastName(shippingAddress.lastname);
            },

            initLoader: function() {
                $(this.checkoutSelector).loader({
                    icon: this.spinnerAssetUrl,
                    texts: {
                        loaderText: 'Please wait while your payment is being verified.'
                    }
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
             * @return {Boolean}
             */
            isSandbox: function() {
                return this.useSimulator;
            },

            getData: function() {
                let parentData = this._super();
                let data = {
                    'method': this.getCode(),
                    'additional_data': {
                        'quote_id': this.quoteId
                    }
                };

                data = _.extend(parentData, data);
                return data;
            },

            /**
             *
             */
            beforePlaceOrder: function() {
              this.initLoader();
              $(this.checkoutSelector).loader('show');
              this.paymentStatusInterval = setInterval(this.verifyPayment.bind(this), 5000);
              this.openWindow();
            },

            /**
             * Open the window for Hosted Checkout.
             *
             * @returns void
             */
            openWindow: function() {
                let w = window.outerWidth - 500,
                    h = window.outerHeight - 500,
                    x = window.outerWidth / 2 + window.screenX - ( w / 2),
                    y = window.outerHeight / 2 + window.screenY - ( h / 2);

                window.open(
                    'about:blank',
                    this.getCode() + '_cc_form',
                    'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='
                    + w +',height=' + h +',left=' + x + ',top=' + y
                );

                $(this.getSelector('cc_form')).submit();
            },

            /**
             * Returns state of place order button
             *
             * @returns {Boolean}
             */
            isButtonActive: function () {
                return this.isActive() && this.isPlaceOrderActionAllowed();
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
             * Show error message
             *
             * @param {String} errorMessage
             * @private
             */
            showError: function (errorMessage) {
                globalMessageList.addErrorMessage({
                    message: errorMessage
                });
            },

            /**
             * Verify the payment was placed
             *
             * @returns void
             */
            verifyPayment: function() {
                let self = this;

                $.ajax({
                    url: this.verificationUrl,
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        quote_id: window.checkoutConfig.quoteData['entity_id']
                    },
                    success: function(response) {
                        if(response.success) {
                            setTimeout(function(){
                                clearInterval(this.paymentStatusInterval);
                                self.placeOrder();
                                self.checkout.loader('hide');
                            }, 3000);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    }
                });
            }
        });
    }
);