<?php

namespace Extremis\Installer;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{

    public function __construct($name = "Extremis Installer", $version = '1.0.0')
    {
        $this->isWordPress();
        parent::__construct($name, $version);
    }

    protected function isWordPress()
    {

        if (!array_reduce(
            ['/package.json', '/composer.json'],
            function ($carry, $file) {
                return $carry && file_exists(getcwd().$file);
            },
            true
        )) {
            (new OutputStyle(new ArgvInput(), new ConsoleOutput))
                ->block("extremis-installer must be called from your theme root.", null, 'error', '  ', true);
            die();
        }
    }

}