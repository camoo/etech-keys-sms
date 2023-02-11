<?php

declare(strict_types=1);

namespace Etech\Sms;

use Etech\Sms\Exception\EtechSmsException;
use Etech\Sms\Exception\HttpClientException;
use Etech\Sms\Http\Client;
use Exception;

/**
 * Class Base
 */
class Base
{
    /*@ var string API endpoint */
    protected $_endPoint = Constants::END_POINT_URL;

    /*@ var object ressource */
    protected static $_dataObject = null;

    /** @var string The resource name as it is known at the server */
    protected $_resourceName = null;

    /* @var mixed credentials */
    protected static $_credentials = [];

    /* @var mixed configs*/
    protected static $_ahConfigs = [];

    /* @var object instance*/
    protected static $_create;

    /** @var string Target version for "Classic" Camoo API */
    protected $camooClassicApiVersion = Constants::END_POINT_VERSION;

    /* @var Array Errors */
    private $_errors = [];

    public function __get(string $property)
    {
        $hPayload = Objects\Base::create()->get($this->getDataObject());

        return $hPayload[$property];
    }

    public function __set(string $property, $value)
    {
        try {
            Objects\Base::create()->set($property, $value, $this->getDataObject());
        } catch (EtechSmsException $err) {
            $this->_errors[] = $err->getMessage();
        }

        return $this;
    }

    public function setResourceName(string $resourceName): void
    {
        $this->_resourceName = $resourceName;
    }

    /** @return string */
    public function getResourceName()
    {
        return $this->_resourceName;
    }

    /**
     * @throws Exception\EtechSmsException
     *
     * @return Objects
     */
    public static function create(?string $api_key = null, ?string $api_secret = null)
    {
        $sConfigFile = dirname(__DIR__) . Constants::DS . 'config' . Constants::DS . 'app.php';
        if ((null === $api_key || null === $api_secret) && !file_exists($sConfigFile)) {
            throw new EtechSmsException(['config' => 'config/app.php is missing!']);
        }
        if (is_file($sConfigFile)) {
            static::$_ahConfigs = (require $sConfigFile);
            static::$_credentials = static::$_ahConfigs['App'];
        }

        if ((null !== $api_key && null !== $api_secret) || empty(static::$_ahConfigs['local_login'])) {
            static::$_ahConfigs = ['local_login' => false, 'App' => ['response_format' => Constants::RESPONSE_FORMAT]];
            static::$_credentials = array_merge(
                static::$_ahConfigs['App'],
                array_combine(Constants::$asCredentialKeyWords, [$api_key, $api_secret])
            );
        }

        $sClass = get_called_class();
        $asCaller = explode('\\', $sClass);
        $sCaller = array_pop($asCaller);
        $sObjecClass = Constants::APP_NAMESPACE . 'Objects\\' . $sCaller;
        if (class_exists($sObjecClass)) {
            static::$_dataObject = new $sObjecClass();
        }

        if ($sClass !== __CLASS__) {
            static::$_create = new $sClass();
        } else {
            static::$_create = new self();
        }

        return static::$_create;
    }

    /** @deprecated 1.3 will be removed in 2.0 */
    public static function clear(): void
    {
        static::$_create = null;
    }

    /** @return object */
    public function getDataObject()
    {
        return self::$_dataObject;
    }

    public function getConfigs(): array
    {
        return self::$_ahConfigs;
    }

    public function getCredentials(): array
    {
        return self::$_credentials;
    }

    /** Returns payload for a request */
    public function getData(string $sValidator = 'default'): array
    {
        try {
            return Objects\Base::create()->get($this->getDataObject(), $sValidator);
        } catch (EtechSmsException $err) {
            $this->_errors[] = $err->getMessage();
        }

        return [];
    }

    /**
     * Returns the CAMOO API URL
     *
     * @author Camoo Sarl
     **/
    public function getEndPointUrl(): string
    {
        $sUrlTmp = $this->_endPoint . Constants::DS . $this->camooClassicApiVersion . Constants::DS;
        $sResource = $this->mapEndpoints($this->getResourceName());

        return sprintf($sUrlTmp . $sResource . '%s', '.' . $this->getResponseFormat());
    }

    /**
     * Execute request with credentials
     *
     * @param mixed|null $oClient
     *
     * @author Camoo Sarl
     */
    public function execRequest(string $sRequestType, bool $bWithData = true, ?string $sObjectValidator = null, $oClient = null)
    {
        $oHttpClient = $oClient === null ? new Client($this->getEndPointUrl(), $this->getCredentials()) : $oClient;
        $data = [];
        if ($bWithData === true && empty($this->getErrors())) {
            $data = null === $sObjectValidator ? $this->getData() : $this->getData($sObjectValidator);
            $oClassObj = $this->getDataObject();
            if (is_object($oClassObj) && $oClassObj instanceof \Etech\Sms\Objects\Message && array_key_exists('to', $data)) {
                $xTo = $data['to'];
                $data['to'] = is_array($xTo) ? implode(',', array_map(Constants::MAP_MOBILE, $xTo)) : $xTo;
            }
        }
        if (!empty($this->getErrors())) {
            throw new EtechSmsException(['_error' => $this->getErrors()]);
        }
        try {
            return $this->decode($oHttpClient->performRequest($sRequestType, $data));
        } catch (HttpClientException $err) {
            throw new EtechSmsException(['_error' => $err->getMessage()]);
        }
    }

    public function setResponseFormat(string $format): void
    {
        static::$_ahConfigs['App']['response_format'] = strtolower($format);
    }

    public function getResponseFormat(): string
    {
        return 'php';
    }

    public function execBulk($hCallBack)
    {
        $oClassObj = $this->getDataObject();
        if (!$oClassObj->has('to') || empty($oClassObj->to)) {
            return false;
        }

        return Lib\Utils::backgroundProcess($this->getData(), $this->getCredentials(), $hCallBack);
    }

    protected function getErrors()
    {
        return $this->_errors;
    }

    /**
     * decode camoo response string
     *
     * @throw EtechSmsException
     *
     * @author Camoo Sarl
     */
    protected function decode(string $sBody): ?\stdClass
    {
        $data = json_decode($sBody);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        if (empty($data)) {
            return null;
        }

        return Lib\Utils::normaliseKeys($data);
    }

    private function mapEndpoints(?string $source = null): string
    {
        return match ($source) {
            'sms' => 'sendsms',
            'balance' => 'api_credit',
            'dlr' => 'dlrsms',
            'view' => 'api_dlr',
            'list' => 'api_transaction',
            default => 'api',
        };
    }
}
