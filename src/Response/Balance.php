<?php
declare(strict_types=1);

namespace Etech\Sms\Response;

/**
 * Class Balance
 *
 * @author CamooSarl
 */
final class Balance extends Base
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
