Requirement
-----------

This library needs minimum requirement for doing well on run.

   - [Sign up](https://www.camoo.cm/join) for a free CAMOO SMS account
   - Ask CAMOO Team for new access_key for developers
   - CAMOO SMS API client for PHP requires version 8.0.x and above

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
	$oMessage->message ='Hello World! Déjà vu!';
	var_dump($oMessage->send());
  ```
##### Send the same SMS to many recipients
            
	- Per request, a max of 50 recipients can be entered.
```php
	$oMessage = \Etech\Sms\Message::create('YOUR_LOGIN', 'YOUR_PASSWORD');
	$oMessage->from ='YourCompany';
	$oMessage->to =['+237612345678', '+237612345679', '+237612345610'];
	$oMessage->message ='Hello World! Déjà vu!';
	var_dump($oMessage->send());
```

Resources
---------

  * [Documentation](https://github.com/camoo/etech-keys-sms/wiki)
  * [Report issues](https://github.com/camoo/etech-keys-sms/issues)
