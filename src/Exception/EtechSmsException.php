<?php
declare(strict_types=1);

namespace Etech\Sms\Exception;

use RuntimeException;

/**
 * Class EtechSmsException
 *
 */
class EtechSmsException extends RuntimeException
{
    /**
     * ERROR status code
     *
     * @const int
     */
    const ERROR_CODE = 500;

    /**
     * Json encodes the message and calls the parent constructor.
     *
     * @param null           $message
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($message = null, int $code = 0, Exception $previous = null)
    {
        if ($code === 0) {
            $code = static::ERROR_CODE;
        }
        parent::__construct(json_encode($message), $code, $previous);
    }
}
