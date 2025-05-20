# MageOS Newsletter Coupon Generator Module for Magento

Add automatically generated coupon management for newsletter subscribers

---

## Overview

The **Newsletter Coupon** module allows you generate a unique coupon for each newsletter subscriber.
The coupon will be sended to the subscriber email after the subscription.


## Features

This module add features to newsletter subscription module
- generate a coupon for each subscription (must be a "SPECIFIC" coupon type salesrule with "Use Auto Generation" enabled)
- send coupon informations to the subscribed user (extending magento newsletter email template)
- controls each coupon expiration. You can change default configuration at ___Store > Configuration > MageOS > Newsletter Coupon > Coupon Expiration Expression___ config path.
- adds graft for integrations with email marketing platforms

## Installation

1. Install it into your Mage-OS/Magento 2 project with composer:
    ```
    composer require mage-os/module-newsletter-coupon
    ```

2. Enable module
    ```
    bin/magento setup:upgrade
    ```



## Configuration

This module comes with standard functionality disabled. You'll need to enable it from configurations on ___Store > Configuration > MageOS > Newsletter Coupon > Enable Coupon Generation___ and connect a valid salesrule. 
You can set the expiration delay time expression you prefer (https://www.php.net/manual/en/datetime.formats.relative.php) on ___Store > Configuration > MageOS > Newsletter Coupon > Coupon Expiration Delay___ path.
This will allow you to generate and link coupon on each new user newsletter subscription.
So create a new SalesRule from your admin panel on __Marketing > Promotions > Cart Price Rules__ calling it "Newsletter Subscription promo" or something like this.
Remember that this salesrule must have "SPECIFIC" coupon type and "Use Auto Generation" checkbox must be flagged. Expiration coupon control comes from the module for each coupon adding regular expression setted on each coupon creation date.
Set the other rule's settings as you like.
Set the new rule id on ___Store > Configuration > MageOS > Newsletter Coupon > Used Sales Rule___.

## Integrations

You can create, delete or update subscriber informations on external platforms with a new module linked with mageOS_NewsletterCoupon.
Requirements:
- if you've installed a 3rd party module that send newsletter email remember to disable magento's from ___Store > Configuration > Customers > Newsletter > Subscription Options > Disable Newsletter * Sending___ config path.
- extend __MageOS\NewsletterCoupon\Model\AbstractNewsletterIntegration__ class on your module adding logic on methods.
- inject your new __Vendor_Module\Model\MyClassName__ with a di argument preference like this:
```
<type name="MageOS\NewsletterCoupon\Api\SubscriberInformationRepositoryInterface">
    <arguments>
        <argument name="marketingEmailIntergrations" xsi:type="array">
            <item name="your_integration_name" xsi:type="object">Vendor\Module\Model\MyClassName</item>
        </argument>
    </arguments>
</type>
```
- That's all, your module is now able to talk with your favorite email marketing platform

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
