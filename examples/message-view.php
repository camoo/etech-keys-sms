<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/vendor/autoload.php';
/**
 * @Brief View Message by message-id
 */
// Step 1: create Message instance
$oSMS = \Etech\Sms\Message::create('YOUR_LOGIN', 'YOUR_PASSWORD');
$oSMS->id = '166156285966156285';
$oSMS->to = '612345678';
$response = $oSMS->view();

var_dump($response);
// Result
/**
 *
class Etech\Sms\Response\Message#5 (3) {
  private $statusCode =>
  int(200)
  private $content =>
  string(75) "{"id":"166156285966156285","etat_sms":"2","Libelle_sms":"DELIVERED"}"
  protected $data =>
  array(4) {
    'status' =>
    string(2) "OK"
    'id' =>
    string(25) "166156285966156285"
    'etat_sms' =>
    string(1) "2"
    'Libelle_sms' =>
    string(9) "DELIVERED"
  }
}
*/

// Get Message Status
var_dump($response->getStatus());
