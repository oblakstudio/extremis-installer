<?php

namespace Extremis\Installer;

use Extremis\Installer\Console\Commands\MetaCommand;

class Installer
{
    /** @var Application */
    public $app;

    public function __construct()
    {
        $app = new Application;
        $app->add(new MetaCommand);
        // $app->add(new PresetCommand);
        // $app->add(new ConfigCommand);
        $this->app = $app;
    }
}
