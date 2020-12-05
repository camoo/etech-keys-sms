<?php
declare(strict_types=1);
namespace Etech\Sms;

/**
 *
 * CAMOO SARL: http://www.camoo.cm
 * @copyright (c) camoo.cm
 * @license: You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Message.php
 * Updated: Jan. 2018
 * Created by: Camoo Sarl (sms@camoo.sarl)
 * Description: CAMOO SMS LIB
 *
 * @link http://www.camoo.cm
 */

/**
 * Class Etech\Sms\Message handles the methods and properties of sending a SMS message.
 *
 */
use Etech\Sms\Exception\EtechSmsException;

class Message extends Base
{

    /**
     * Send Message
     *
     * @return mixed Message Response
     * @throws Exception\EtechSmsException
     */
    public function send()
    {
        try {
            return $this->execRequest(HttpClient::POST_REQUEST);
        } catch (EtechSmsException $err) {
            throw new EtechSmsException($err->getMessage());
        }
    }

    /**
     * view a sent message
     *
     * @throws Exception\EtechSmsException
     *
     * @return mixed Message
     */
    public function view()
    {
        try {
            $this->setResourceName(Constants::RESOURCE_VIEW);
            return $this->execRequest(HttpClient::GET_REQUEST, true, Constants::RESOURCE_VIEW);
        } catch (EtechSmsException $err) {
            throw new EtechSmsException($err->getMessage());
        }
    }

    public function getStatus() : string
    {
        $this->setResourceName(Constants::RESOURCE_STATUS);
        return $this->execRequest(HttpClient::GET_REQUEST, true, Constants::RESOURCE_STATUS);
    }

    /**
     * sends bulk message
     *
     * @return integer|bool
     */
    public function sendBulk($hCallBack=[])
    {
        $xResp = $this->execBulk($hCallBack);
        return !empty($xResp)? $xResp : false;
    }
}
