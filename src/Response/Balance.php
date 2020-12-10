<?php
declare(strict_types=1);

namespace Etech\Sms\Response;

use Etech\Sms\Http\Response;

/**
 * Class Balance
 *
 * @author CamooSarl
 */
final class Balance extends Response
{
    public function getValue()
    {
        if (array_key_exists('balance', $this->data)) {
            return round($this->data['balance'], 2);
        }

        return 0.00;
    }

    public function getCurrency()
    {
        if (array_key_exists('currency', $this->data)) {
            return $this->data['currency'];
        }

        return '';
    }
}
