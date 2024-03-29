<?php

declare(strict_types=1);

namespace Etech\Sms\Objects;

use Etech\Sms\Exception\EtechSmsException;
use Etech\Sms\Lib\Utils;
use Valitron\Validator;

/**
 * Class Objects\Base
 */
class Base
{
    protected static $_create = null;

    public static function create()
    {
        if (is_null(static::$_create)) {
            static::$_create = new self();
        }

        return static::$_create;
    }

    public function set(string $sProperty, $value, $oClass = null)
    {
        if ($oClass === null) {
            return;
        }
        if (!property_exists($oClass, $sProperty)) {
            throw new EtechSmsException([$sProperty => 'is not allowed!']);
        }
        if ($sProperty === 'from') {
            $value = Utils::clearSender($value);
        }
        if ($sProperty === 'to') {
            $value = Utils::makeNumberE164Format($value);
        }

        $oClass->$sProperty = $value;
    }

    public function get($oClass, $validator = 'default')
    {
        if (empty($oClass)) {
            return [];
        }
        $hPayload = get_object_vars($oClass);
        if (method_exists($oClass, 'validator' . ucfirst($validator))) {
            $sValidator = 'validator' . ucfirst($validator);
            $oValidator = $oClass->$sValidator(new Validator($hPayload));
            if ($oValidator->validate() === false) {
                throw new EtechSmsException($oValidator->errors());
            }
        }
        if (array_key_exists('route', $hPayload) && $hPayload['route'] === 'classic' && $oClass instanceof \Etech\Sms\Objects\Message && array_key_exists('to', $hPayload)) {
            $asTo = $hPayload['to'];
            foreach ($asTo as $xTo) {
                $sTo = is_array($xTo) && !empty($xTo['mobile']) ? $xTo['mobile'] : $xTo;
                if (!Utils::isValidPhoneNumber($sTo, 'CM', true)) {
                    throw new EtechSmsException([$sTo => 'does not seems to be a cameroonian phone number!']);
                }
            }
        }

        return array_filter($hPayload);
    }

    public function isMTNCameroon($oValidator, string $sParam): void
    {
        $oValidator
            ->rule(function ($field, $value, $params, $fields) {
                if (empty($value)) {
                    return false;
                }

                return Utils::isCmMTN($value);
            }, $sParam)->message('{field} is not carried by MTN Cameroon');
    }

    public function isValidUTF8Encoded($oValidator, string $sParam): void
    {
        $oValidator
            ->rule(function ($field, $value, $params, $fields) {
                return mb_check_encoding($value, 'UTF-8');
            }, $sParam)->message('{field} needs to be a valid UTF-8 encoded string');
    }

    public function notEmptyRule($oValidator, string $sParam): void
    {
        $oValidator
            ->rule(function ($field, $value, $params, $fields) {
                return !empty($value);
            }, $sParam)->message('{field} can not be blank/empty...');
    }

    // @codeCoverageIgnoreEnd

    public function isPossibleNumber($oValidator, string $sParam): void
    {
        $oValidator
            ->rule(function ($field, $value, $params, $fields) {
                if (empty($value)) {
                    return false;
                }

                foreach ($value as $xTo) {
                    $sTo = is_array($xTo) && !empty($xTo['mobile']) ? $xTo['mobile'] : $xTo;
                    $xTel = preg_replace('/[^\dxX]/', '', $sTo);
                    $xTel = ltrim($xTel, '0');
                    if (!is_numeric($xTel) || mb_strlen($xTel) < 9 || mb_strlen($xTel) > 15) {
                        return false;
                    }
                }

                return true;
            }, $sParam)->message('{field} no (correct) phone number found!');
    }

    public function has(string $sName): bool
    {
        return property_exists($this, $sName);
    }
}
