<?php
declare(strict_types=1);

namespace Etech\Sms\Response;

/**
 * Class Message
 * @author CamooSarl
 */
final class Message extends Base
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
}
