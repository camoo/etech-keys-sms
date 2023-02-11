<?php

declare(strict_types=1);
require_once dirname(__DIR__) . '/vendor/autoload.php';
/**
 * @Brief View Message by message-id
 */
// Step 1: create Message instance
$oSMS = \Etech\Sms\Message::create('YOUR_LOGIN', 'YOUR_PASSWORD');
$oSMS->id = '166156285966156285';
$response = $oSMS->dlr();

var_dump($response);
// Result
/**
 * class Etech\Sms\Response\Message#5 (3) {
 * private $statusCode =>
 * int(200)
 * private $content =>
 * string(114) "{"id":"166156285966156285","date_reception":"2020-12-05 12:29:28","etat_sms":"2","Libelle_sms":"DELIVERED"}"
 * protected $data =>
 * array(5) {
 * 'status' =>
 * string(2) "OK"
 * 'id' =>
 * string(25) "166156285966156285"
 * 'date_reception' =>
 * string(19) "2020-12-05 12:29:28"
 * 'etat_sms' =>
 * string(1) "2"
 * 'Libelle_sms' =>
 * string(9) "DELIVERED"
 * }
 * }
 */

// Get Message Status
var_dump($response->getStatus());

// Get Status time
var_dump($response->getStatusTime());
