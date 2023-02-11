<?php

declare(strict_types=1);

namespace Etech\Sms\Http;

use Camoo\Http\Curl\Domain\Client\ClientInterface;
use Camoo\Http\Curl\Domain\Entity\Configuration;
use Camoo\Http\Curl\Infrastructure\Client as CurlClient;
use Camoo\Http\Curl\Infrastructure\Request;
use Etech\Sms\Constants;
use Etech\Sms\Exception\HttpClientException;
use Throwable;
use Valitron\Validator;

/**
 * Class Client
 */
class Client
{
    public const GET_REQUEST = 'GET';

    public const POST_REQUEST = 'POST';

    protected string $endpoint;

    protected array $userAgent = [];

    protected array $hRequestVerbs = [self::GET_REQUEST => 'query', self::POST_REQUEST => 'form_params'];

    private int $timeout = Constants::CLIENT_TIMEOUT;

    private array $hAuthentication = [];

    private array $_headers = [];

    private Configuration $clientConfiguration;

    /**
     * @param int $timeout > 0
     *
     * @throws HttpClientException if timeout settings are invalid
     */
    public function __construct(string $endpoint, array $hAuthentication, int $timeout = 0)
    {
        $this->endpoint = $endpoint;
        $this->hAuthentication = $hAuthentication;
        $this->addUserAgentString(Constants::getPhpVersion());

        if (!is_int($timeout) || $timeout < 0) {
            throw new HttpClientException(sprintf(
                'Connection timeout must be an int >= 0, got "%s".',
                is_object($timeout) ? get_class($timeout) : gettype($timeout) . ' ' . var_export($timeout, true)
            ));
        }
        if (!empty($timeout)) {
            $this->timeout = $timeout;
        }
        $this->clientConfiguration = new Configuration($this->timeout);
    }

    public function addUserAgentString(string $userAgent): void
    {
        $this->userAgent[] = $userAgent;
    }

    /**
     * @param string|null $data
     *
     *@throws HttpClientException
     *
     * @return false|string
     */
    public function performRequest(
        string $method,
        array $data = [],
        array $headers = [],
        ?ClientInterface $client = null
    ) {
        $this->setHeader($headers);
        //VALIDATE HEADERS
        $hHeaders = $this->getHeaders();
        $sMethod = strtoupper($method);
        $oValidator = new Validator(array_merge(['request' => $sMethod, 'response_format' => 'json'], $hHeaders));
        if (empty($this->validatorDefault($oValidator))) {
            throw new HttpClientException(json_encode($oValidator->errors()));
        }
        $auth = $this->getAuthKeys();

        $dataMapping = [
            'login' => $auth['api_key'],
            'password' => $auth['api_secret'],
        ];

        if (array_key_exists('to', $data)) {
            $dataMapping['destinataire'] = $data['to'];
        }

        if (array_key_exists('id', $data)) {
            $dataMapping['id'] = $data['id'];
        }

        if (array_key_exists('message', $data)) {
            $dataMapping = array_merge([
                'sender_id' => $data['from'],
                'message' => $data['message'],
            ], $dataMapping);

            if (array_key_exists('reference', $data)) {
                $dataMapping['ext_id'] = $data['reference'];
            }

            if (array_key_exists('datacoding', $data) && $data['datacoding'] === 'unicode') {
                $dataMapping['unicode'] = 1;
            }

            if (array_key_exists('programmation', $data)) {
                $dataMapping['programmation'] = $data['programmation'];
            }
        }

        try {
            $request = new Request($this->clientConfiguration, $this->endpoint, $hHeaders, $dataMapping, $sMethod);
            $client = $client ?? new CurlClient($this->clientConfiguration);

            $response = $client->sendRequest($request);

            if ($response->getStatusCode() === 200) {
                $result = ['result' => trim((string)$response->getBody())];

                return json_encode($result);
            }
            throw new HttpClientException();
        } catch (Throwable $exception) {
            throw new HttpClientException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    protected function getUserAgentString(): string
    {
        return implode(' ', $this->userAgent);
    }

    protected function getAuthKeys(): array
    {
        return $this->hAuthentication;
    }

    protected function setHeader(array $option = []): void
    {
        $this->_headers += $option;
    }

    protected function getHeaders(): array
    {
        $default = [];
        if ($hAuth = $this->getAuthKeys()) {
            $default = [
                'X-Api-Key' => $hAuth['api_key'],
                'X-Api-Secret' => $hAuth['api_secret'],
                'User-Agent' => $this->getUserAgentString(),
            ];
        }

        return $this->_headers += $default;
    }

    /** Validate request params */
    private function validatorDefault(Validator $oValidator): bool
    {
        $oValidator->rule('required', ['X-Api-Key', 'X-Api-Secret', 'response_format']);
        $oValidator->rule('optional', ['User-Agent']);
        $oValidator->rule('in', 'response_format', ['json', 'xml']);

        return $oValidator->rule('in', 'request', array_keys($this->hRequestVerbs))->validate();
    }
}
