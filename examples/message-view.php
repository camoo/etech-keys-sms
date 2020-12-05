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
var_dump($oSMS->view());

// Result
/**
 *
class stdClass#5 (3) {
  public $id =>
  string(25) "166156285966156285"
  public $etat_sms =>
  string(1) "2"
  public $Libelle_sms =>
  string(9) "DELIVERED"
}
*/
