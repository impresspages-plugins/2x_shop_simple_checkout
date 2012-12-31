<div class="ipmOption">
    <div class="ipmOption">
        <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/product_title') ?></span>
        <input type="text" class="ipAdminInput ipaFieldTitle" name="label" value="<?php echo isset($title) ? $this->esc($title) : '' ?>" />
    </div>
    <div class="ipmOption">
        <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/currency') ?></span>
        <input type="text" class="ipAdminInput ipaFieldCurrency" name="label" value="<?php echo isset($currency) ? $this->esc($currency) : '' ?>" />
    </div>
    <div class="ipmOption">
        <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/price') ?></span>
        <input type="text" class="ipAdminInput ipaFieldPrice" name="label" value="<?php echo isset($price) ? $this->esc($price) : '' ?>" />
    </div>
    <div class="ipmOption">
        <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/success_url') ?></span>
        <input type="text" class="ipAdminInput ipaFieldSuccessUrl" name="label" value="<?php echo isset($successUrl) ? $this->esc($successUrl) : '' ?>" />
    </div>
    <div class="ipmOption">
        <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/require_login') ?></span>
        <input type="checkbox" class="ipaFieldRequireLogin" name="label" <?php echo !empty($requireLogin) ? 'checked="checked"' : "" ?>/>
    </div>
    <div class="ipmOption">
        <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/product_id') ?></span>
        <input type="text" class="ipAdminInput ipaFieldProductId" name="label" value="<?php echo isset($productId) ? $this->esc($productId): '' ?>" />
    </div>
</div>
<br/><br/>

<b><?php echo $this->escPar('shop/simple_checkout/admin_translations/payment_methods') ?></b><br/><br/>
<div class="ipmPaymentMethods">
    <div class="ipmPaymentMethod" data-paymentMethod="paypal">
        <a href="#" class="ipaButton ipmMove ipaMove"><?php echo $this->escPar('standard/content_management/widget_contact_form/move'); ?></a>
        <div class="ipmOptions">
            <img src="<?php echo BASE_URL.PLUGIN_DIR.'shop/simple_checkout/public/paypal.png' ?>" />
            <div class="ipmOption">
                <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/active') ?></span>
                <input type="checkbox" class="ipaFieldFieldPayPalActive" name="active" <?php if (!empty($paypalActive)) { ?>checked="checked"<?php } ?> />
            </div>
            <div class="ipmOption">
                <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/email') ?></span>
                <input type="text" class="ipAdminInput ipaFieldPayPalEmail" name="email" value="<?php echo isset($paypalEmail) ? $this->esc($paypalEmail): '' ?>" />
            </div>
        </div>
    </div>
    <div class="ipmPaymentMethod" data-paymentMethod="google">
        <a href="#" class="ipaButton ipmMove ipaMove"><?php echo $this->escPar('standard/content_management/widget_contact_form/move'); ?></a>
        <div class="ipmOptions">
            <img src="<?php echo BASE_URL.PLUGIN_DIR.'shop/simple_checkout/public/google-checkout.gif' ?>" />
            <div class="ipmOption">
                <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/active') ?></span>
                <input type="checkbox" class="ipaFieldFieldGoogleActive" name="active" <?php if (!empty($googleActive)) { ?>checked="checked"<?php } ?>  />
            </div>
            <div class="ipmOption">
                <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/merchant_id') ?></span>
                <input type="text" class="ipAdminInput ipaFieldGoogleMerchantId" name="merchantId" value="<?php echo isset($googleMerchantId) ? $this->esc($googleMerchantId): '' ?>" />
            </div>
            <div class="ipmOption">
                <span class="ipmLabel"><?php echo $this->escPar('shop/simple_checkout/admin_translations/merchant_key') ?></span>
                <input type="text" class="ipAdminInput ipaFieldGoogleMerchantKey" name="merchantKey" value="<?php echo isset($googleMerchantKey) ? $this->esc($googleMerchantKey): '' ?>" />
            </div>
        </div>
    </div>

</div>