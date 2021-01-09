<?php

namespace Extremis\Installer\Transformations;

use Illuminate\Support\Collection;

class ThemeHeaders
{
    protected $content;

    public $stylesheet;

    public $headers = [
        'Name'        => 'Extremis Child Theme',
        'URI'         => 'https://oblak.studio/portfolio/',
        'Description' => 'Child theme for Parent theme',
        'Author'      => 'Oblak Studio',
        'Author URI'  => 'https://oblak.studio',
        'Template'    => 'Parent Theme Name',
        'Version'     => '2.0',
        'Text Domain' => 'extremis',
    ];

    public function __construct($stylesheet = '')
    {
        $this->headers = new Collection($this->headers);
        $this->stylesheet = $stylesheet ?: getcwd().'/style.css';
    }

    public function getCurrentHeaders()
    {
        $this->content = file_get_contents($this->stylesheet);
        $this->headers->transform(function ($value, $field) {
            preg_match('/^.*'.preg_quote($field, '/').'[^:]*:(.*)$/mi', $this->content, $matches);
            return $matches && $matches[1] ? trim($matches[1]) : $value;
        });
        return $this;
    }

    public function replaceHeaders($headers)
    {
        $content = str_replace($this->headers->all(), $headers->all(), $this->content);
        file_put_contents($this->stylesheet, $content);
    }
}
