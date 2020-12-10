<?php
declare(strict_types=1);
namespace Etech\Sms\Objects;

/**
 * CAMOO SARL: http://www.camoo.cm
 * @copyright (c) camoo.cm
 * @license: You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Objects/Message.php
 * Description: CAMOO SMS message Objects
 *
 * @link http://www.camoo.cm
 */

use Valitron\Validator;
use Etech\Sms\Exception\EtechSmsException;

final class Message extends Base
{

    /**
     * An unique random ID which is created on Camoo SMS
     * platform and is returned for the created object.
     *
     * @var string
     */
    protected $id;

    /**
    * The sender of the message. This can be a telephone number
    * (including country code) or an alphanumeric string. In case
    * of an alphanumeric string, the maximum length is 11 characters.
    *
    * @var string
    */
    public $from;

    /**
     * The content of the SMS message.
     *
     * @var string
     */
    public $message;

    /**
     * Recipient that sould receive the sms
     * You can set single recipient (string) or multiple recipients by using array
     *
     * @var string | Array
     */
    public $to;

    /**
     * The datacoding used, can be plain or unicode
     *
     * @var string
     */
    public $datacoding;

    /**
     * A client reference. It might be whatever you want to identify the your message.
     *
     * @var string
     */
    public $reference;

    /**
     * The amount of seconds, that the message is valid. If a message is not delivered within this time, the message will be discarded. Should be greater than 30
     *
     * @var integer
     */
    public $programmation;

    public function validatorDefault(Validator $oValidator) : Validator
    {
        $oValidator
            ->rule('required', ['from', 'message', 'to']);
        $oValidator
            ->rule('optional', ['datacoding','reference', 'programmation']);
        $oValidator
            ->rule('in', 'datacoding', ['plain','unicode']);
        $oValidator
            ->rule('lengthMax', 'reference', 32);
        $oValidator
            ->rule('integer', 'programmation');
        $oValidator
            ->rule('min', 'programmation', 30);
        $oValidator
            ->rule(function ($field, $value, $params, $fields) {
                return file_exists($value);
            }, 'pgp_public_file')->message("{field} does not exist");
        $this->isPossibleNumber($oValidator, 'to');
        $this->isValidUTF8Encoded($oValidator, 'from');
        $this->isValidUTF8Encoded($oValidator, 'message');
        return $oValidator;
    }

    public function validatorView(Validator $oValidator) : Validator
    {
        $oValidator
            ->rule('required', ['id', 'to']);
        $this->notEmptyRule($oValidator, 'id');
        $this->isPossibleNumber($oValidator, 'to');
        return $oValidator;
    }

    public function validatorDlr(Validator $oValidator) : Validator
    {
        $oValidator
            ->rule('required', ['id']);
        $this->notEmptyRule($oValidator, 'id');
        return $oValidator;
    }
}
