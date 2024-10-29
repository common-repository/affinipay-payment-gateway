=== AffiniPay WordPress ===
Contributors: usharmaap, wesleyaaronhuntap, jjungmannap, affinipay0msluyter 
Tags: payments, affinipay
Requires at least: 4.9.1
Tested up to: 6.5.2
Requires PHP: 7.0
License: GPLv2 or later
Stable Tag: 1.0.9
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Make Credit Card or eCheck payments using the AffiniPay Payment Gateway

== Description ==
Make Credit Card or eCheck payments using the AffiniPay Payment Gateway from any page on your website.

== Usage ==
1. Activate the AffiniPay WordPress Plugin
2. Enter your merchant secret key on the plugin settings page (Settings -> AffiniPay Payments) in WordPress admin.
3. Select Merchant account from "Account" dropdown which is differentiated  by icon based on account type.
4. When you have multiple accounts then please Select a merchant account from the "Account" dropdown, it will auto generate shortcode with account type and account id. If your account has not been setup for eCheck payments
contact AffiniPay customer service
5. Copy the affinipay-payment shortcode that is generated below the "Account" dropdown to any page or post within your website.
[affinipay-payment type=creditcard account=pM_hSdsc39DcXSabO8TBFs-g]

== Short Code Options & Usage Examples ==
`[affinipay-payment]` this shortcode can be used in the content editor on any page from your website to display
a payment form that will include all fields configured in your accounts payment policy by default.

If you need to further customize your payment form the following attributes are available

| Option  | Default Value | Valid Values | Comments |
| --- | --- | --- | --- |
| **Required** | -- | -- | -- |
| `amount` | 0 | ( float ) | The amount of the transaction (optional if amount_field is used) |
| `amount_field` | no | ( yes / no ) | Display the amount field on the payment form (optional if amount is used) |
| `type` | creditcard | ( creditcard / echeck ) | The charge type, creditcard for Credit Card payments, or echeck for bank payments |
| **Payment Form Fields** | -- | -- | -- |
| `reference_field` | no | ( yes / no ) | Display the payment reference field useful for the customer to enter an invoice number for example. |
| `customer_email_field` | yes | ( yes / no ) | Display the customer email field, this is the email address that your customer receipt will be sent. |
| `customer_address_field` | no | ( yes / no ) | Display the customer address field on the payment form |
| `customer_address2_field` | no | ( yes / no ) | Display the customer address2 field on the payment form |
| `customer_city_field` | no | ( yes / no ) | Display the customer city field on the payment form |
| `customer_state_field` | no | ( yes / no ) | Display the customer state field on the payment form |
| `customer_postalcode_field` | no | ( yes / no ) | Display the customer postal code field on the payment form when account type is echeck |
| `customer_phone_field` | no | ( yes / no ) | Display the customer phone field on the payment form |
| **Titles & Labels** | -- | -- | -- |
| `title` | Make a Payment | ( Any alphanumeric ) | The title to display on the page |
| `button_text` | Submit Payment | ( Any alphanumeric ) | The text of the payment form submit button |

== Usage Examples ==
Simple Payment form with specified amount
`[affinipay-payment type=creditcard amount=19.95]`

== Available Action Hooks ==
| Hook | Description |
| --- | --- |
| `affinipay_charge_success` | Called when a new charge has successfully completed. The charge is passed as an argument to this callback |
| `affinipay_charge_error` | Called when a charge fails. An exception is passed as an argument to the callback |

== Available Filter Hooks ==
| Filter | Description / Use Case |
| --- |
| `affinipay_before_payment_form` | Add content before the payment form is rendered |
| `affinipay_after_payment_form` | Add custom content after the payment form |
| `affinipay_before_render_customer_fields` | Add custom content before the customer fields are rendered |
| `affinipay_after_render_customer_fields` | Add content after the customer fields on the payment form |
| `affinipay_before_render_amount_field` | Add content before the amount field |
| `affinipay_after_render_amount_field` | Add content after the amount field |
| `affinipay_before_render_reference_field` | Add content before the payment reference field |
| `affinipay_after_render_reference_field` | Add custom content after the payment reference field |
| `affinipay_before_render_email_field` | Add custom content before the email field |
| `affinipay_after_render_email_field` | Add custom content after the email field |
| `affinipay_before_render_name_field` | Add custom content before the customer name field |
| `affinipay_after_render_name_field` | Add custom content after the customer name field |
| `affinipay_before_render_address_field` | Add custom content before the address field |
| `affinipay_after_render_address_field` | Add custom content after the address field |
| `affinipay_before_render_address2_field` | Add custom content before the address2 field |
| `affinipay_after_render_address2_field` | Add custom content after the address2 field |
| `affinipay_before_render_city_field` | Add custom content before the city field |
| `affinipay_after_render_city_field` | Add custom content after the city field |
| `affinipay_before_render_state_field` | Add custom content before the state field |
| `affinipay_after_render_state_field` | Add custom content after the state field |
| `affinipay_before_postalcode_field` | Add custom content before the postal code field |
| `affinipay_after_postalcode_field` | Add custom content after the postal code field |
| `affinipay_before_render_phone_field` | Add custom content before the phone field |
| `affinipay_after_render_phone_field` | Add custom content after the phone field |
| `affinipay_before_render_payment_fields` | Add custom content before the credit card field |
| `affinipay_after_render_payment_fields` | Add custom content after the credit card fields |

== Upgrade Notice ==
The AffiniPay WordPress plugin no longer requires your public key and secret key, now only the secret key is required. Be sure to set
the secret key on the plugin settings page (Settings -> AffiniPay Payments) in WordPress admin.

== Frequently Asked Questions ==
Q: Can I use a Credit Card and an eCheck payment shortcode on the same page?
A: Yes, you may generate any number of shortcodes and include them on your page in any order

Q: How do I set an amount on a payment that does not change?
A: Use the "amount" key/value in the payment shortcode, see examples above.

== Changelog ==

= 1.0 =
* Initial release

= 1.0.2 =
* Multi-payment support

= 1.0.3 =
* Bug fixes

= 1.0.4 =
* Bug fixes

= 1.0.5 =
* Bug fix for payment amount in shortcode

= 1.0.6 =
* Support Wordpress 5.5. Bug fixes.

= 1.0.7 =
* Bug fixes

= 1.0.8 =
* Bug fixes for incorrect echeck accountID's
