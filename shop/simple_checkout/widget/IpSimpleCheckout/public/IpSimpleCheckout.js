/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


/**
 * Widget initialization
 */
function IpWidget_IpSimpleCheckout(widgetObject) {
    this.widgetObject = widgetObject;

    this.manageInit = manageInit;
    this.prepareData = prepareData;

    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        var widgetObject = this.widgetObject;

        this.widgetObject.find('.ipmPaymentMethods').sortable();
        this.widgetObject.find('.ipmPaymentMethods').sortable('option', 'handle', '.ipaMove');


        var $paymentMethods = this.widgetObject.find('.ipmPaymentMethod');

        $paymentMethods.detach().sort(function(a, b) {
            var aMethod = $(a).data('paymentmethod');
            var bMethod = $(b).data('paymentmethod');
            var aPriority = instanceData.data[aMethod + '_priority'];
            var bPriority = instanceData.data[bMethod + '_priority'];
            return aPriority > bPriority;
        });

        this.widgetObject.find('.ipmPaymentMethods').append($paymentMethods);
    }


    function prepareData() {
        var data = Object();
        var $widgetObject = this.widgetObject;

        data.title = $widgetObject.find('.ipaFieldTitle').val();
        data.currency = $widgetObject.find('.ipaFieldCurrency').val();
        data.price = $widgetObject.find('.ipaFieldPrice').val();
        data.successUrl = $widgetObject.find('.ipaFieldSuccessUrl').val();
        data.requireLogin = $widgetObject.find('.ipaFieldRequireLogin').is(':checked')?1:0;
        data.productId = $widgetObject.find('.ipaFieldProductId').val();

        data.paypalActive = $widgetObject.find('.ipaFieldFieldPayPalActive').is(':checked')?1:0;
        data.paypalEmail = $widgetObject.find('.ipaFieldPayPalEmail').val();
        data.paypalPriority = $widgetObject.find('.ipmPaymentMethod[data-paymentmethod=paypal]').index();

        data.googleActive = $widgetObject.find('.ipaFieldFieldGoogleActive').is(':checked')?1:0;
        data.googleMerchantId = $widgetObject.find('.ipaFieldGoogleMerchantId').val();
        data.googleMerchantKey = $widgetObject.find('.ipaFieldGoogleMerchantKey').val();
        data.googlePriority = $widgetObject.find('.ipmPaymentMethod[data-paymentmethod=google]').index();

        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }



};



