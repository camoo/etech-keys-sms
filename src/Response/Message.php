<?php
declare(strict_types=1);

namespace Etech\Sms\Response;

use Etech\Sms\Http\Response;

use DateTime;
use DateTimeZone;

/**
 * Class Message
 * @author CamooSarl
 */
final class Message extends Response
{
    public function getId()
    {
        if (array_key_exists('id', $this->data)) {
            return $this->data['id'];
        }

        if (array_key_exists('message_id', $this->data)) {
            return $this->data['message_id'];
        }

        return 0;
    }

    public function getState()
    {
        if (array_key_exists('etat_sms', $this->data)) {
            return (int) $this->data['etat_sms'];
        }
        return 0;
    }

    public function getStatus()
    {
        if (array_key_exists('Libelle_sms', $this->data)) {
            return strtolower($this->data['Libelle_sms']);
        }

        return 'unknow';
    }

    public function getStatusTime()
    {
        if (!array_key_exists('date_reception', $this->data)) {
            return null;
        }

        if (empty($this->data['date_reception'])) {
            return null;
        }
        return new DateTime($this->data['date_reception'], new DateTimeZone('Africa/Douala'));
    }
}
