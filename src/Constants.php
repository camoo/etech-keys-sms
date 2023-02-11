<?php

declare(strict_types=1);

namespace Etech\Sms;

/**
 * Class Constants
 */
class Constants
{
    public const CLIENT_VERSION = '1.3';

    public const CLIENT_TIMEOUT = 30; // 10 sec

    public const MIN_PHP_VERSION = 70100;

    public const DS = '/';

    public const END_POINT_URL = 'https://sms.etech-keys.com';

    public const END_POINT_VERSION = 'ss';

    public const APP_NAMESPACE = '\\Etech\\Sms\\';

    public const RESOURCE_VIEW = 'view';

    public const RESOURCE_LIST = 'list';

    public const RESOURCE_STATUS = 'dlr';

    public const RESOURCE_BALANCE = 'balance';

    public const RESPONSE_FORMAT = 'json';

    public const ERROR_PHP_VERSION = 'Your PHP-Version belongs to a release that is no longer supported. You should upgrade your PHP version as soon as possible, as it may be exposed to unpatched security vulnerabilities';

    public const SMS_MAX_RECIPIENTS = 50;

    public const CLEAR_OBJECT = [\Etech\Sms\Base::class, 'clear'];

    public const MAP_MOBILE = [\Etech\Sms\Lib\Utils::class, 'mapMobile'];

    public const MAP_E164FORMAT = [\Etech\Sms\Lib\Utils::class, 'phoneNumberE164Format'];

    public const PERSONLIZE_MSG_KEYS = ['%NAME%'];

    public static $asCredentialKeyWords = ['api_key', 'api_secret'];

    public static function getPhpVersion(): string
    {
        if (!defined('PHP_VERSION_ID')) {
            $version = explode('.', PHP_VERSION); //@codeCoverageIgnore
            define('PHP_VERSION_ID', $version[0] * 10000 + $version[1] * 100 + $version[2]); //@codeCoverageIgnore
        }

        if (PHP_VERSION_ID < static::MIN_PHP_VERSION) {
            trigger_error(static::ERROR_PHP_VERSION, E_USER_ERROR);//@codeCoverageIgnore
        }

        return 'PHP/' . PHP_VERSION_ID;
    }

    public static function getSMSPath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }
}
