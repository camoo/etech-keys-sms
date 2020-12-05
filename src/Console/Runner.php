<?php
declare(strict_types=1);
namespace Etech\Sms\Console;

use Etech\Sms\Exception\EtechSmsException;

class Runner
{
    public function run($argv)
    {
        if (isset($argv)) {
            $sPASS = $argv[1];
            if ($asPASS = \Etech\Sms\Lib\Utils::decodeJson(base64_decode($sPASS), true)) {
                $hCallBack = $asPASS[0];
                $sTmpName  = $asPASS[1];
                $hCredentials = $asPASS[2];
                $sFile = \Etech\Sms\Constants::getSMSPath(). 'tmp/' .$sTmpName;
                if (file_exists($sFile) && is_readable($sFile)) {
                    if (($sData = file_get_contents($sFile)) && ($hData = \Etech\Sms\Lib\Utils::decodeJson($sData, true))) {
                        unlink($sFile);
                        \Etech\Sms\Lib\Utils::doBulkSms($hData, $hCredentials, $hCallBack);
                    }
                }
            }
        }
    }
}
