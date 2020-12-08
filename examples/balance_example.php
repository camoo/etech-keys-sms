<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/vendor/autoload.php';
/**
 * @Brief read current balance
 *
 */
// Step 1: create balance instance
$oBalance = \Etech\Sms\Balance::create('YOUR_LOGIN', 'YOUR_PASSWORD');

// Step2: retrieve your current balance
$response = $oBalance->get();
var_dump($response);

// output:
/*
     class Etech\Sms\Response\Balance#4 (3) {
      private $statusCode =>
      int(200)
      private $content =>
      string(42) "{"balance" : 5.000000, "currency" : "Units"}"
      protected $data =>
      array(3) {
        'status' =>
        string(2) "OK"
        'balance' =>
        double(5)
        'currency' =>
        string(5) "Units"
      }
    }
*/

// Get Balance Value
var_dump($response->getValue());
// 5.00
