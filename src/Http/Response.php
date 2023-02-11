<?php

declare(strict_types=1);

namespace Etech\Sms\Http;

use Etech\Sms\Lib\Utils;

/**
 * Class Response
 *
 * @author CamooSarl
 */
class Response
{
    /** @var string */
    public const BAD_STATUS = 'KO';

    /** @var string */
    public const GOOD_STATUS = 'OK';

    /** @var array $data */
    protected $data;

    /** @var int $statusCode */
    private $statusCode;

    /** @var string $content */
    private $content;

    public function __construct(string $content = '', int $statusCode = 200)
    {
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->data = $this->getJson($content);
    }

    /** @return string */
    public function getBody()
    {
        return (string)$this->content;
    }

    /** @return int */
    public function getStatusCode()
    {
        return (int)$this->statusCode;
    }

    /** @return string */
    public function getResponseStatus()
    {
        if (array_key_exists('status', $this->data)) {
            return $this->data['status'];
        }

        return static::BAD_STATUS;
    }

    /** @return array */
    public function getJson()
    {
        if ($this->getStatusCode() !== 200) {
            $message = $this->content !== '' ? $this->content : 'request failed!';

            return ['status' => static::BAD_STATUS, 'message' => $message];
        }
        $result = $this->decodeJson($this->content, true);

        return array_merge(['status' => static::GOOD_STATUS], $result);
    }

    /**
     * @return array|stdClass
     */
    protected function decodeJson(string $sJSON, bool $bAsHash = false)
    {
        if ($this->content === '') {
            return $bAsHash === false ? (object)[] : [];
        }

        return Utils::decodeJson($sJSON, $bAsHash);
    }
}
