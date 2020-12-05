<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/vendor/autoload.php';
/**
 * Send a sms
 */
$oMessage = \Etech\Sms\Message::create('YOUR_LOGIN', 'YOUR_PASSWORD');
$oMessage->from ='YourCompany';
$oMessage->to = '+237612345678';
$oMessage->reference = 'api' . time();
$oMessage->message ='Hello Kmer World! Déjà vu!';
var_dump($oMessage->send());

// Result
/**
 class stdClass#50 (1) {
  public $message_id =>
  string(25) "1661562859661562859927940" // message ID
}
 */
