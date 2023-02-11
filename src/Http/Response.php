<?php

declare(strict_types=1);

namespace Etech\Sms\Http;

use Etech\Sms\Lib\Utils;
use stdClass;

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

    protected array $data;

    private int $statusCode;

    private string $content;

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

    public function getJson(): array
    {
        if ($this->getStatusCode() !== 200) {
            $message = $this->content !== '' ? $this->content : 'request failed!';

            return ['status' => static::BAD_STATUS, 'message' => $message];
        }
        $result = $this->decodeJson($this->content, true);

        return array_merge(['status' => static::GOOD_STATUS], $result);
    }

    protected function decodeJson(string $sJSON, bool $bAsHash = false): stdClass|array
    {
        if ($this->content === '') {
            return $bAsHash === false ? (object)[] : [];
        }

        return Utils::decodeJson($sJSON, $bAsHash);
    }
}
