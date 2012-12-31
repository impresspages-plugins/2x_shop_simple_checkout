Testimonial Plugin for ImpressPages CMS
Compatible with version 2.4+


HOW TO INSTALL
===============
1. Upload this plugin "shop" folder to "ip_plugins/" directory of your website.
2. Login to administration area
3. Go to "Developer -> Modules" and press "install".
4. You should see Simple Checkout widget in ContentManagement tab. Place it anywhere you like and fill in required data

GOOGLE CHECKOUT ADDITIONAL STEPS
================
If you want to use Google Checkout, you should create google checkout account.
 Then open Settings -> Integration tab:

1. Take Merchant ID and Key information and put into widget appropriate fields
2. Set API callback URL to: http://example.com/?g=shop&m=simple_checkout&a=googleCallback
   Replace example.com to your domain
3. Set "Callback contents" to "Notification Serial Number"
4. Set API version to 2.0. Script should work with any version. But it has been tested with 2.0
5. Your currency in Google checkout account should be the same as set in SimpleCheckout widget.
