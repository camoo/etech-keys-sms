<?php

declare(strict_types=1);

namespace Etech\Sms\Objects;

/**
 * CAMOO SARL: http://www.camoo.cm
 *
 * @copyright (c) camoo.cm
 * @license You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Objects/Balance.php
 * updated: Jan 2018
 * Description: CAMOO SMS message Objects
 *
 * @link http://www.camoo.cm
 */
use Valitron\Validator;

final class Balance extends Base
{
    /**
     * Phonenumber.
     * Only available for MTN Mobile Money Cameroon
     */
    public string $phonenumber;

    /** amount that should be recharged */
    public ?float $amount = null;

    public function validatorDefault(Validator $oValidator): Validator
    {
        $oValidator
            ->rule('required', ['phonenumber', 'amount']);
        $oValidator
            ->rule('integer', 'amount');
        $this->isMTNCameroon($oValidator, 'phonenumber');

        return $oValidator;
    }
}
