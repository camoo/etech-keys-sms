<?php
declare(strict_types=1);
namespace Etech\Sms\Http;

use Etech\Sms\Exception\HttpClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7;
use Valitron\Validator;
use Etech\Sms\Lib\Utils;
use Etech\Sms\Constants;

/**
 * Class Client
 */
class Client
{
    const GET_REQUEST = 'GET';
    const POST_REQUEST = 'POST';

    /** @var string $endpoint */
    protected $endpoint;

    /**
     * @var array
     */
    protected $userAgent = [];

    /**
     * @var array
     */
    protected $hRequestVerbs = [self::GET_REQUEST => 'query', self::POST_REQUEST => 'form_params'];

    /**
     * @var int
     */
    private $timeout = Constants::CLIENT_TIMEOUT;

    /**
    * @var mixed
    */
    private $hAuthentication = [];

    /**
     * @var array
     */
    private $_headers = [];

    /**
     * @param string $endpoint
     * @param int $timeout > 0
     *
     * @throws HttpClientException if timeout settings are invalid
     */
    public function __construct(string $endpoint, array $hAuthentication, int $timeout = 0)
    {
        $this->endpoint = $endpoint;
        $this->hAuthentication = $hAuthentication;
        $this->addUserAgentString($this->getAPIInfo());
        $this->addUserAgentString(Constants::getPhpVersion());

        if (!is_int($timeout) || $timeout < 0) {
            throw new HttpClientException(sprintf(
                'Connection timeout must be an int >= 0, got "%s".',
                is_object($timeout) ? get_class($timeout) : gettype($timeout).' '.var_export($timeout, true)
            ));
        }
        if (!empty($timeout)) {
            $this->timeout = $timeout;
        }
    }

    /**
     * Validate request params
     *
     * @param Validator $oValidator
     *
     * @return boolean
     */
    private function validatorDefault(Validator $oValidator) : bool
    {
        $oValidator->rule('required', ['X-Api-Key', 'X-Api-Secret', 'response_format']);
        $oValidator->rule('optional', ['User-Agent']);
        $oValidator->rule('in', 'response_format', ['json', 'xml']);
        return $oValidator->rule('in', 'request', array_keys($this->hRequestVerbs))->validate();
    }

    /**
     * @param string $userAgent
     */
    public function addUserAgentString(string $userAgent) : void
    {
        $this->userAgent[] = $userAgent;
    }

    /**
     * @return strin userAgentString
     */
    protected function getUserAgentString() : string
    {
        return implode(' ', $this->userAgent);
    }

    /**
     * @param string      $method
     * @param string|null $data
     *
     * @return array
     *
     * @throws HttpClientException
     */
    public function performRequest(string $method, array $data = [], array $headers = [], $oClient=null)
    {
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
                'login'    => $auth['api_key'],
                'password' => $auth['api_secret'],
            ];

        if (array_key_exists('to', $data)) {
            $phoneNumber = Utils::getNumberProto($data['to'], 'CM');
            $dataMapping['destinataire'] = $phoneNumber->getNationalNumber();
        }

        if (array_key_exists('id', $data)) {
            $dataMapping['id'] = $data['id'];
        }

        if (array_key_exists('message', $data)) {
            $dataMapping = array_merge([
                'sender_id'     => $data['from'],
                'message' 	    => $data['message'],
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
            $client = null === $oClient? new GuzzleClient(['timeout' => $this->timeout]) : $oClient;
            $oResponse = $client->request(
                $sMethod,
                $this->endpoint,
                [$this->hRequestVerbs[$sMethod] => $dataMapping, 'headers' => $hHeaders]
            );
            if ($oResponse->getStatusCode() === 200) {
                $result = ['result' => trim((string) $oResponse->getBody())];
                return json_encode($result);
            }
            throw new HttpClientException();
        } catch (RequestException $e) {
            throw new HttpClientException(Psr7\str($e->getRequest()));
        }
    }

    protected function getAuthKeys() : array
    {
        return $this->hAuthentication;
    }

    protected function setHeader(array $option = []) : void
    {
        $this->_headers += $option;
    }

    protected function getHeaders() : array
    {
        $default = [];
        if ($hAuth = $this->getAuthKeys()) {
            $default = [
                'X-Api-Key'    => $hAuth['api_key'],
                'X-Api-Secret' => $hAuth['api_secret'],
                'User-Agent'   => $this->getUserAgentString()
            ];
        }
        return $this->_headers += $default;
    }

    protected function getEndPointFormat() : string
    {
        $asEndPoint = explode('.', $this->endpoint);
        return end($asEndPoint);
    }

    protected function getAPIInfo() : string
    {
        $sIdentity = 'EtechSms/ApiClient/';
        if (defined('WP_CAMOO_SMS_VERSION')) {
            $sWPV = '';
            global $wp_version;
            if ($wp_version) {
                $sWPV = $wp_version;//@codeCoverageIgnore
            }
            $sIdentity = 'WP'.$sWPV.'/CamooSMS' .WP_CAMOO_SMS_VERSION .Constants::DS;
        }
        return $sIdentity .Constants::CLIENT_VERSION;
    }
}
