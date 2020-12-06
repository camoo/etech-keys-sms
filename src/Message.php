<?php
declare(strict_types=1);
namespace Etech\Sms;

/**
 * CAMOO SARL: http://www.camoo.cm
 *
 * @copyright (c) camoo.cm
 * @license: You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Message.php
 * Created by: Camoo Sarl (sms@camoo.sarl)
 * Description: CAMOO SMS LIB
 *
 * @link http://www.camoo.cm
 */

use Etech\Sms\Exception\EtechSmsException;
use Etech\Sms\Lib\Utils;
use Etech\Sms\Response\Message as MessageResponse;

/**
 * Class Etech\Sms\Message handles the methods and properties of sending a SMS message.
 */
class Message extends Base
{

    /**
     * Send Message
     *
     * @return mixed Message Response
     *
     * @throws Exception\EtechSmsException
     */
    public function send()
    {
        try {
            $this->setResourceName('sms');
            $response = $this->execRequest(HttpClient::POST_REQUEST);
            return new MessageResponse(sprintf('{"message_id" : "%s"}', $response->result));
        } catch (EtechSmsException $err) {
            return new MessageResponse($err->getMessage(), $err->getCode());
        }
    }

    /**
     * Views a sent message with status
     *
     * @throws Exception\EtechSmsException
     *
     * @return mixed Message
     */
    public function view()
    {
        try {
            $this->setResourceName(Constants::RESOURCE_VIEW);
            $response = $this->execRequest(HttpClient::GET_REQUEST, true, Constants::RESOURCE_VIEW);
            return new MessageResponse($response->result);
        } catch (EtechSmsException $err) {
            return new MessageResponse($err->getMessage(), $err->getCode());
        }
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
