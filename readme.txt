Simple Checkout Plugin for ImpressPages CMS
Compatible with version 2.4+


HOW TO INSTALL
===============
1. Upload this plugin "shop" folder to "ip_plugins/" directory of your website.
2. Login to administration area
3. Go to "Developer -> Modules" and press "install".
4. You should see Simple Checkout widget in ContentManagement tab. Place it anywhere you like and fill in required data

ATTENTION
================
Payment method configuration options are changed once for all widgets. If you change
PayPal email or Google checkout credentials in one widget, they change for all other widgets
too *IMMEDIATELLY WHEN YOU PRESS "CONFIRM" WITHOUT EVEN PRESSING PUBLISH*.

TESTING
================
To test plugin, change value of DEVELOPMENT_ENVIRONMENT constant in ip_config.php to 1.
This instructs plugin to use sandbox version of PayPal and GoogleCheckout.


CONFIGURATION
================
All configuration options are available in "Developer -> Modules config" tab, "Simple Checkout" section.
Most of those configuration options can be changed within widget.
To change checkout button images, set "image Url" value in "Developer -> Modules config"


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


CHECK PLUGIN HEALTH ON LIVE
================
- On production website make sure "DEVELOPMENT_ENVIRONMENT" value in ip_config.php is set to 0.
- Check if click on checkout button redirects user to Google/PayPal