<?php

declare(strict_types=1);

namespace Etech\Sms\Console;

use Etech\Sms\Exception\BackgroundProcessException;

class BackgroundProcess
{
    public function __construct(private ?string $command = null)
    {
    }

    public function run($sOutputFile = '/dev/null', $bAppend = false)
    {
        if ($this->getCommand() === null) {
            throw new BackgroundProcessException('Command is missing');
        }

        $sOS = $this->getOS();

        if (empty($sOS)) {
            throw new BackgroundProcessException('Operating System cannot be determined');
        }

        if (str_starts_with($sOS, 'WIN')) {
            shell_exec(sprintf('%s &', $this->getCommand(), $sOutputFile));

            return 0;
        } elseif ($sOS === 'LINUX' || $sOS === 'FREEBSD' || $sOS === 'DARWIN') {
            return (int)shell_exec(sprintf('%s %s %s 2>&1 & echo $!', $this->getCommand(), ($bAppend) ? '>>' : '>', $sOutputFile));
        }

        throw new BackgroundProcessException('Operating System not Supported');
    }

    protected function getOS()
    {
        return strtoupper(PHP_OS);
    }

    protected function getCommand()
    {
        if (null !== $this->command) {
            return escapeshellcmd($this->command);
        }

        return null;
    }
}
