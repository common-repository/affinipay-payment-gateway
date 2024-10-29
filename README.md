# AffiniPay WordPress Plugin

Make payments using the AffiniPay Payment Gateway from any page on your website.

### Requirements
* AffiniPay / LawPay Merchant Account
* SSL Certificate - Your website must have a valid SSL certificate installed to process live transactions. The plugin will work in test mode without a certificate.

### Installation
Copy the affinipay-wordpress plugin directory to the plugins folder of your WordPress install.

### Getting Started
1. Activate the AffiniPay WordPress Plugin
2. Enter your keys for the AffiniPay Payment Gateway section of Settings in the WordPress admin.
3. Configure the general options for your payment form behavior
4. Add the `[affinipay-payment amount=29.95]` shortcode to any page or post within your website.


#### Short Code Options & Usage Examples
`[affinipay-payment]` this shortcode can be used in the content editor on any page from your website to display
a payment form that will include all fields configured in your accounts payment policy by default.

If you need to further customize your payment form the following attributes are available

| Option  | Default Value | Valid Values | Comments |
| --- | --- | --- | --- |
| **Required** | -- | -- | -- |
| `amount` | 0 | ( float ) | The amount of the transaction (optional if amount_field is used) |
| `amount_field` | no | ( yes / no ) | Display the amount field on the payment form (optional if amount is used) |
| **Payment Form Fields** | -- | -- | -- |
| `customer_fields` | no | ( yes / no ) | Display all of the customer related fields on the payment form (a 'yes' here overrides 'no' values on individual fields) |
| `reference_field` | no | ( yes / no ) | Display the payment reference field useful for the customer to enter an invoice number for example. |
| `customer_email_field` | yes | ( yes / no ) | Display the customer email field, this is the email address that your customer receipt will be sent. |
| `customer_name_field` | yes | ( yes / no ) | Display the customer name field on the payment form |
| `customer_address_field` | no | ( yes / no ) | Display the customer address field on the payment form |
| `customer_address2_field` | no | ( yes / no ) | Display the customer address2 field on the payment form |
| `customer_city_field` | no | ( yes / no ) | Display the customer city field on the payment form |
| `customer_state_field` | no | ( yes / no ) | Display the customer state field on the payment form |
| `customer_postalcode_field` | no | ( yes / no ) | Display the customer postal code field on the payment form |
| `customer_phone_field` | no | ( yes / no ) | Display the customer phone field on the payment form |
| **Titles & Labels** | -- | -- | -- |
| `title` | Make a Payment | ( Any alphanumeric ) | The title to display on the page |
| `button_text` | Submit Payment | ( Any alphanumeric ) | The text of the payment form submit button |
|  **Recurring Charges**  | -- | -- | -- |
| `recurs` | no | WEEKLY,MONTHLY,YEARLY | The frequency that the recurring charge will occur |
| `recurring_interval` | 1 | ( integer ) | The next time a transaction should occur.  For example: A value of 3 would create a charge that recurs every 3 (weeks, months, years) |
| `recurring_description` | null | (Any Alphanumeric) | A custom description for the recurring charge |
#### Usage Examples:
Simple Payment form with specified amount
`[affinipay-payment amount=19.95]`

Display all customer fields with a set amount of $19.95
`[affinipay-payment amount=19.95 customer_fields=yes]`

Display amount field Donation pages can allow users to enter a custom amount.
`[affinipay-payment]`

####Recurring Charges
Recurring charge of $100.00 once a month
`[affinipay-payment amount=100.00 recurs=MONTHLY]`

Recurring charge of $250.00 every three months
`[affinipay-payment amount=250.00 recurs=MONTHLY recurring_interval=3]`


#### Available Action Hooks
| Hook | Description |
| --- | --- |
| `affinipay_charge_success` | Called when a new charge has successfully completed. The charge is passed as an argument to this callback |
| `affinipay_charge_error` | Called when a charge fails. An exception is passed as an argument to the callback |

#### Available Filter Hooks
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


# Running in docker

### Install and trust root certificate

On a mac:
	
* Double click on `docker-resources/ssl-certificates/afpDockerRootCA.pem` and follow the prompt to install the cerificate in your login keychain.
* Open `Keychain Access.app`, find the certificate installed in the previous step, double click to view certificate details, and under `Trust` change `When using this certificate` to `Always Trust`.

![trust certificate](./wp-plugin-root-cert-premissions.png)
### Install Node.js and NPM
* Via NVM: http://dev.topheman.com/install-nvm-with-homebrew-to-use-multiple-versions-of-node-and-iojs-easily/, or
* Via installer: https://nodejs.org/en/download/

NOTE: Known to work with Node.js versions 10.15.3 and 8.9.0

### Bring up
Run `./bringup` from the root directory.

This will bring up a Wordpress environment with:
* Url `https://localhost:8466`
* Admin user with username `admin` user and password `pass`
* Current plugin code on this branch built and installed but not enabled
* Wordpress files mounted at `./site/wordpress`

### Teardown
Run `./teardown` from the root directory.
