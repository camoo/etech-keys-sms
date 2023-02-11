<?php

declare(strict_types=1);
namespace Etech\Sms\Console;

use Etech\Sms\Constants;
use Etech\Sms\Lib\Utils;

class Runner
{
    public function run(array $argv): void
    {
        $sPASS = $argv[1];
        if ($asPASS = Utils::decodeJson(base64_decode($sPASS), true)) {
            $hCallBack = $asPASS[0];
            $sTmpName = $asPASS[1];
            $hCredentials = $asPASS[2];
            $sFile = Constants::getSMSPath() . 'tmp/' . $sTmpName;
            if (!file_exists($sFile) || !is_readable($sFile)) {
                return;
            }
            if (($sData = file_get_contents($sFile)) && ($hData = Utils::decodeJson($sData, true))) {
                unlink($sFile);
                Utils::doBulkSms($hData, $hCredentials, $hCallBack);
            }
        }
    }
}
