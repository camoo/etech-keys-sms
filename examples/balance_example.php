<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/vendor/autoload.php';
/**
 * @Brief read current balance
 *
 */
// Step 1: create balance instance
$oMessage = \Etech\Sms\Message::create('YOUR_LOGIN', 'YOUR_PASSWORD');

// Step2: retrieve your current balance
var_dump($oBalance->get());

// output:
/*
class stdClass#29 (1) {
  public $result =>
  string(1) "6000"
}
)*/
