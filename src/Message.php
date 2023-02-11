<?php

declare(strict_types=1);
/**
 * CAMOO SARL: http://www.camoo.cm
 *
 * @copyright (c) camoo.cm
 *
 * @license: You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Message.php
 * Created by: Camoo Sarl (sms@camoo.sarl)
 * Description: CAMOO SMS LIB
 *
 * @link http://www.camoo.cm
 */

namespace Etech\Sms;

use Etech\Sms\Exception\EtechSmsException;
use Etech\Sms\Http\Client;
use Etech\Sms\Response\Message as MessageResponse;

/**
 * Class Etech\Sms\Message handles the methods and properties of sending a SMS message.
 */
class Message extends Base
{
    /**
     * Sends Message
     *
     * @return MessageResponse
     */
    public function send()
    {
        try {
            $this->setResourceName('sms');
            $response = $this->execRequest(Client::POST_REQUEST);

            return new MessageResponse($response->result);
        } catch (EtechSmsException $err) {
            return new MessageResponse($err->getMessage(), $err->getCode());
        }
    }

    /**
     * Views a sent message with status
     *
     * @deprecated 1.2 Use dlr() instead
     *
     * @return MessageResponse
     */
    public function view()
    {
        try {
            $this->setResourceName(Constants::RESOURCE_VIEW);
            $response = $this->execRequest(Client::GET_REQUEST, true, Constants::RESOURCE_VIEW);

            return new MessageResponse($response->result);
        } catch (EtechSmsException $err) {
            return new MessageResponse($err->getMessage(), $err->getCode());
        }
    }

    /**
     * Grabs DLR of a scent message with status
     *
     * @return MessageResponse
     */
    public function dlr()
    {
        try {
            $this->setResourceName(Constants::RESOURCE_STATUS);
            $response = $this->execRequest(Client::POST_REQUEST, true, Constants::RESOURCE_STATUS);

            return new MessageResponse($response->result);
        } catch (EtechSmsException $err) {
            return new MessageResponse($err->getMessage(), $err->getCode());
        }
    }

    /**
     * sends bulk message
     *
     * @return int|bool
     */
    public function sendBulk($hCallBack = [])
    {
        $xResp = $this->execBulk($hCallBack);

        return !empty($xResp) ? $xResp : false;
    }
}
