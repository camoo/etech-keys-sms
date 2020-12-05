<p align="center">
  <a href="https://www.camoo.cm/bulk-sms" target="_blank" >
    <img alt="CamooSms" src="https://www.camoo.hosting/img/logos/logoDomain.png"/>
  </a>
</p>
<p align="center">
	PHP SMS API Sending SMS via the <strong><em>Etech-Keys SMS gateway</em></strong>
</p>

Requirement
-----------

This library needs minimum requirement for doing well on run.

   - [Sign up](https://www.camoo.cm/join) for a free CAMOO SMS account
   - Ask CAMOO Team for new access_key for developers
   - CAMOO SMS API client for PHP requires version 7.0.x and above

## Installation via Composer

Package is available on [Packagist](https://packagist.org/packages/camoo/etech-keys-sms),
you can install it using [Composer](http://getcomposer.org).

```shell
composer require etech/sms
```
Quick Examples
--------------

##### Sending a SMS
```php
	$oMessage = \Etech\Sms\Message::create('YOUR_LOGIN', 'YOUR_PASSWORD');
	$oMessage->from ='YourCompany';
	$oMessage->to = '+237612345678';
	$oMessage->message ='Hello Kmer World! Déjà vu!';
	var_dump($oMessage->send());
  ```
##### Send the same SMS to many recipients
            
	- Per request, a max of 50 recipients can be entered.
```php
	$oMessage = \Etech\Sms\Message::create('YOUR_LOGIN', 'YOUR_PASSWORD');
	$oMessage->from ='YourCompany';
	$oMessage->to =['+237612345678', '+237612345679', '+237612345610'];
	$oMessage->message ='Hello Kmer World! Déjà vu!';
	var_dump($oMessage->send());
```
##### Sending Bulk SMS from your Script
It is obvious that sending bulk data to any system is a problem! Therefore you should check our recommendation for the best approach
   - (_**[See example for bulk sms](https://github.com/camoo/sms/wiki/How-to-send-Bulk-SMS-from-your-script#send-sms-sequentially)**_)

WordPress Plugin
----------------
If you are looking for a powerful WordPress plugin to send SMS, then download our [wp-camoo-sms](https://github.com/camoo/wp-camoo-sms)

Resources
---------

  * [Documentation](https://github.com/camoo/etech-keys-sms/wiki)
  * [Report issues](https://github.com/camoo/etech-keys-sms/issues)
