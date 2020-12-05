<?php
declare(strict_types=1);
namespace Etech\Sms;

/**
 * CAMOO SARL: http://www.camoo.cm
 * @copyright (c) camoo.cm
 * @license: You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Balance.php
 * Created by: Camoo Sarl (sms@camoo.sarl)
 * Description: CAMOO SMS LIB
 *
 * @link http://www.camoo.cm
 */

/**
 * Class Etech\Sms\Balance
 * Get or add balance to your account
 */
use Etech\Sms\Exception\EtechSmsException;

class Balance extends Base
{

    /**
    * read the current user balance
    *
    * @throws Exception\EtechSmsException
    * @return mixed Balance
    */
    public function get()
    {
        try {
            $this->setResourceName(Constants::RESOURCE_BALANCE);
            return $this->execRequest(HttpClient::GET_REQUEST, false);
        } catch (EtechSmsException $err) {
            throw new EtechSmsException('Balance Request can not be performed!');
        }
    }
}