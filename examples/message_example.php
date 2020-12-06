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

$response = $oMessage->send();
var_dump($response);

// Result
/**
class Etech\Sms\Response\Message#5 (3) {
  private $statusCode =>
  int(200)
  private $content =>
  string(75) "{"message_id":"166156285966156285"}"
  protected $data =>
  array(2) {
    'status' =>
    string(2) "OK"
    'message_id' =>
    string(25) "166156285966156285"
  }
}
 */

// Get Message ID
var_dump($response->getId());
// 166156285966156285
