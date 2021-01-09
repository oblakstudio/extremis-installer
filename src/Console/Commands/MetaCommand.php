<?php

namespace Extremis\Installer\Console\Commands;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Extremis\Installer\Console\Exceptions\ConfigureCommandException;
use Extremis\Installer\Transformations\ThemeHeaders;

class MetaCommand extends Command
{
    /** @var Collection */
    protected $options;

    /** {@inheritdoc} */
    protected $description = 'Sets theme headers and other metadata.';

    /** @var ThemeHeaders */
    public $themeHeaders;

    /** {@inheritdoc} */
    protected function configure()
    {
        parent::configure();
        $this->themeHeaders = new ThemeHeaders("{$this->root}/style.css");
        $this->gatherOptionsData();
        $this->addOptions();
    }

    /** {@inheritdoc} */
    protected function validate()
    {
        if (!file_exists($stylesheet = $this->themeHeaders->stylesheet)) {
            throw new ConfigureCommandException("FILE NOT FOUND:\n    {$stylesheet}");
        }
    }

    protected function gatherOptionsData()
    {
        $this->themeHeaders->getCurrentHeaders();
        $this->options = (new Collection($this->themeHeaders->headers))
            ->map(function ($value, $key) {
                $slug = Str::slug($key, '_');
                return [
                    'header'   => new Collection(compact('key', 'value')),
                    'option'   => "theme_{$slug}",
                    'question' => "Theme {$key}",
                    'default'  => $value
                ];
            });
    }

    protected function addOptions()
    {
        $this->options->each(function ($data) {
            extract($data);
            $this->addOption($option, null, InputOption::VALUE_REQUIRED, $question, $default);
        });
    }

    /** {@inheritdoc} */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->options->map(function ($data) {
            extract($data);
            $data['header']['value'] = $this->option($option) === $default
                                     ? $this->ask($question, $default)
                                     : $this->option($option);
            return $data;
        });
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $headers = $this->options
            ->pluck('header')
            ->flatMap(function ($header) {
                extract($header->all());
                return [$key => $value];
            });
        $this->themeHeaders->replaceHeaders($headers);
    }
}
